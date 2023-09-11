# Strict mode
$ErrorActionPreference = "Stop"
$ProgressPreference = "SilentlyContinue"

# Check if 'ip' command is available
$ipCommand = Get-Command ip -ErrorAction SilentlyContinue

if ($ipCommand) {
    $HostIp = (ip -4 addr show docker0 | Select-String -Pattern '(?<=inet\s)\d+(\.\d+){3}').Matches.Value
} else {
    $HostIp = (ipconfig | Select-String -Pattern "1[97]2.*" -Context 0,1).Context.PostContext | ForEach-Object { $_.Trim() }
}

# Here we go!
$ImportantColor = [System.ConsoleColor]::Red
$NcColor = [System.ConsoleColor]::Black

$ResetDatabases = $false

$AppDirectory = Get-Location

$Databases = @("hms_local_db", "hms_local_test_db")
$GenerateDummyData = $true
$MySqlUser = "root"
$MySqlPassword = "password"
$DockerCommandPrefix = ""

# Quick and dirty check for --docker-root flag
if ($args -contains "--docker-root") {
    $DockerCommandPrefix = "sudo "
}

$DatabaseCommandPrefix = "${DockerCommandPrefix}docker-compose exec -T database mysql -u $MySqlUser -p`"$MySqlPassword`" --batch --skip-column-names -e"

# Quick and dirty check for --reset flag
if ($args -contains "--reset") {
    $ResetDatabases = $true

    # Confirm that they mean to include the reset flag
    $confirmation = Read-Host "This will drop and recreate your local databases. Press 'y' to confirm:"
    if ($confirmation -ne "y" -and $confirmation -ne "Y") {
        # They did not confirm, bail out
        Write-Host "Aborted"
        exit 0
    }
}

# Quick and dirty check for --local-db flag
if ($args -contains "--local-db") {
    $DatabaseCommandPrefix = "mysql -u $MySqlUser --batch --skip-column-names -p`"$MySqlPassword`" -e"
}

# Create .env files
Write-Host "Checking if .env files exist"

if (!(Test-Path "${AppDirectory}\.env")) {
    Copy-Item "${AppDirectory}\.env.example" "${AppDirectory}\.env"
}

Set-Location "${AppDirectory}\docker"

Set-Content -Path .env -Value "HOST_IP=$HostIp"

# Bring up docker
Write-Host "Stopping docker if it's currently running"
Invoke-Expression "${DockerCommandPrefix}docker-compose stop"

Write-Host "Bringing up docker"
Invoke-Expression "${DockerCommandPrefix}docker-compose up -d --remove-orphans"

# Check DB is ready
Write-Host "Waiting for database container to be ready..."

for ($i = 1; $i -le 6; $i++) {
    if ($i -eq 6) {
        Write-Host "Timeout while waiting for database to come up"
        exit 1
    }
    $dbExists = Invoke-Expression "$DatabaseCommandPrefix '' 2> nul"
    if ($?) {
        break
    }

    if ($i -eq 6) {
        # Try and bring up the database in case it didn't get started automatically
        Invoke-Expression "${DockerCommandPrefix}docker-compose exec -T database /etc/init.d/mysql start"
    }
    Start-Sleep -Seconds 5
}

# Create databases
Write-Host "Checking state of databases"

foreach ($database in $Databases) {
    if ($ResetDatabases) {
        Invoke-Expression "$DatabaseCommandPrefix `"DROP DATABASE IF EXISTS $database;`""
    }

    $dbExists = Invoke-Expression "$DatabaseCommandPrefix `"SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database';`" 2>&1 | Select-String -Pattern "mysql:" -NotMatch

    if ($dbExists -eq "1") {
        Write-Host "$database exists, skipping"
        continue
    }

    Write-Host "Creating $database"
    Invoke-Expression "$DatabaseCommandPrefix `"CREATE DATABASE $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`""
}

Write-Host "Updating composer"
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app composer install"

Write-Host "Clearing cache"
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan cache:clear"
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan config:clear"
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan view:clear"
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan optimize:clear"

Write-Host "Running initial migrations"
# migration
Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan migrate"

# generate initial data
# if ($GenerateDummyData) {
#     Invoke-Expression "${DockerCommandPrefix}docker exec -it hms-app php artisan generate:dummy-data"
# }

Write-Host "All Done!"
Write-Host "Go to http://api.hms-dev.com/ to see it in action"
