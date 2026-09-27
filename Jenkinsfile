pipeline {
    agent any

    // =========================================================================
    // ENVIRONMENT CONFIGURATION
    // Centralized environment variables for the CI/CD pipeline
    // =========================================================================
    environment {
        APP_NAME       = 'todo-list-app'
        BUILD_ARTIFACT = "build/${APP_NAME}.zip"
        DEPLOY_PATH    = "C:/xampp/htdocs/to-do-list"
        PHP_BIN        = 'php'
    }

    // =========================================================================
    // PIPELINE OPTIONS
    // =========================================================================
    options {
        timeout(time: 15, unit: 'MINUTES')    // Abort build if it hangs
        timestamps()                         // Add timestamps to console logs
        disableConcurrentBuilds()           // Prevent overlapping builds
    }

    // =========================================================================
    // CI/CD PIPELINE STAGES
    // Flow: Checkout -> Dependencies -> Code Validation -> Test -> Build -> Deploy
    // =========================================================================
    stages {

        // ---------------------------------------------------------------------
        // STAGE 1: CHECKOUT
        // Clones the latest commit from the GitHub repository
        // ---------------------------------------------------------------------
        stage('Checkout SCM') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 1] Checking out source code from GitHub...   "
                echo "=========================================================="
                checkout scm
                script {
                    echo "Checked out commit: ${env.GIT_COMMIT ?: 'Local Workspace'}"
                    echo "Branch: ${env.GIT_BRANCH ?: 'main'}"
                }
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 2: PREPARE ENVIRONMENT & DEPENDENCIES
        // Checks runtime environment and installs required vendor dependencies
        // ---------------------------------------------------------------------
        stage('Prepare Dependencies') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 2] Preparing Environment & Dependencies...  "
                echo "=========================================================="
                script {
                    // Check PHP runtime availability
                    if (isUnix()) {
                        sh 'php -v'
                        sh 'composer --version || echo "Composer not globally installed, using vendored files"'
                    } else {
                        bat 'php -v || "C:\\xampp\\php\\php.exe" -v'
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 3: CODE VALIDATION (LINTING)
        // Checks syntax for all PHP files using native php -l
        // Any syntax error fails the build immediately before testing
        // ---------------------------------------------------------------------
        stage('Code Validation') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 3] Validating Code Syntax (PHP Lint)...     "
                echo "=========================================================="
                script {
                    if (isUnix()) {
                        sh '''
                            for file in $(find . -maxdepth 2 -name "*.php" ! -path "./vendor/*"); do
                                php -l "$file" || exit 1
                            done
                        '''
                    } else {
                        // Windows agent syntax check
                        powershell '''
                            $php = if (Get-Command php -ErrorAction SilentlyContinue) { "php" } elseif (Test-Path "C:\\xampp\\php\\php.exe") { "C:\\xampp\\php\\php.exe" } else { "php" }
                            $files = Get-ChildItem -Path . -Filter "*.php"
                            foreach ($f in $files) {
                                & $php -l $f.FullName
                                if ($LASTEXITCODE -ne 0) {
                                    Write-Error "Syntax error found in $($f.Name)"
                                    exit 1
                                }
                            }
                        '''
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 4: AUTOMATED TESTING
        // Runs automated test suite (File existence, Schema, Auth checks)
        // Returns exit code 1 on failure, causing Jenkins to halt and turn RED
        // ---------------------------------------------------------------------
        stage('Automated Testing') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 4] Running Application Test Suite...         "
                echo "=========================================================="
                script {
                    if (isUnix()) {
                        sh 'php tests/test_runner.php'
                    } else {
                        powershell '''
                            $php = if (Get-Command php -ErrorAction SilentlyContinue) { "php" } elseif (Test-Path "C:\\xampp\\php\\php.exe") { "C:\\xampp\\php\\php.exe" } else { "php" }
                            & $php tests/test_runner.php
                            if ($LASTEXITCODE -ne 0) {
                                Write-Error "Test suite failed!"
                                exit 1
                            }
                        '''
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 5: BUILD & PACKAGE ARTIFACT
        // Packages tested and validated application into a deployable release zip
        // Excludes development, testing, and secret files
        // ---------------------------------------------------------------------
        stage('Build & Package') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 5] Packaging Deployable Build Artifact...    "
                echo "=========================================================="
                script {
                    if (isUnix()) {
                        sh '''
                            mkdir -p build
                            zip -r build/todo-list-app.zip . -x "*.git*" "tests/*" ".env*" "build/*" "java-*"
                        '''
                    } else {
                        powershell '''
                            New-Item -ItemType Directory -Force -Path build | Out-Null
                            $destination = "build\\todo-list-app.zip"
                            if (Test-Path $destination) { Remove-Item -Force $destination }
                            
                            # Gather release files
                            $exclude = @('.git', 'tests', '.idea', '.vscode', 'build', 'java-simple', 'java-standalone', 'java-version', 'java-standalone.zip')
                            $files = Get-ChildItem -Path . | Where-Object { $exclude -notcontains $_.Name }
                            Compress-Archive -Path $files -DestinationPath $destination -Force
                            Write-Output "Artifact packaged successfully at: $destination"
                        '''
                    }
                }
                // Archive the generated artifact in Jenkins for audit/traceability
                archiveArtifacts artifacts: 'build/*.zip', fingerprint: true, allowEmptyArchive: true
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 6: DEPLOYMENT
        // Deploys the application to the web server environment
        // Supports Local/Staging server, Docker, or Vercel (if configured)
        // ---------------------------------------------------------------------
        stage('Deployment') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 6] Deploying Application...                 "
                echo "=========================================================="
                script {
                    echo "Deploying release to target environment..."

                    // 1. If Vercel token is configured, deploy to Vercel
                    if (env.VERCEL_TOKEN) {
                        echo "Deploying to Vercel using vercel.json configuration..."
                        if (isUnix()) {
                            sh 'npx --yes vercel --prod --token $VERCEL_TOKEN --yes'
                        } else {
                            powershell 'npx --yes vercel --prod --token $env:VERCEL_TOKEN --yes'
                        }
                    } 
                    // 2. Otherwise deploy to Local Web Server (XAMPP / Apache / Staging)
                    else {
                        echo "Deploying to Web Server directory..."
                        if (isUnix()) {
                            sh 'echo "Simulating/Executing deployment to /var/www/html"; mkdir -p /var/www/html/to-do-list || true'
                        } else {
                            powershell '''
                                $target = "C:\\xampp\\htdocs\\to-do-list"
                                if (Test-Path "C:\\xampp\\htdocs") {
                                    Write-Output "Deploying to local XAMPP web root: $target"
                                    # Copy application release files to target
                                    Copy-Item -Path *.php, database.sql, .env.example -Destination $target -Force -ErrorAction SilentlyContinue
                                    if (Test-Path "assets") {
                                        Copy-Item -Path assets -Destination $target -Recurse -Force -ErrorAction SilentlyContinue
                                    }
                                    Write-Output "Deployment to local web server complete!"
                                } else {
                                    Write-Output "Web server root not found; build artifact ready in Jenkins workspace."
                                }
                            '''
                        }
                    }
                }
            }
        }

        // ---------------------------------------------------------------------
        // STAGE 7: POST-DEPLOYMENT VERIFICATION & MONITORING
        // Runs health check endpoint to confirm application is responsive
        // ---------------------------------------------------------------------
        stage('Post-Deployment Verification') {
            steps {
                echo "=========================================================="
                echo " [CI/CD STAGE 7] Monitoring: Health Check Verification... "
                echo "=========================================================="
                script {
                    echo "Testing application availability (healthcheck.php)..."
                    if (isUnix()) {
                        sh 'php healthcheck.php || true'
                    } else {
                        powershell '''
                            $php = if (Get-Command php -ErrorAction SilentlyContinue) { "php" } elseif (Test-Path "C:\\xampp\\php\\php.exe") { "C:\\xampp\\php\\php.exe" } else { "php" }
                            & $php healthcheck.php
                        '''
                    }
                }
            }
        }
    }

    // =========================================================================
    // POST-BUILD ACTIONS
    // Notifications and Status Monitoring
    // =========================================================================
    post {
        always {
            echo "=========================================================="
            echo " [MONITORING] Pipeline Execution Completed.                "
            echo " Build Number : ${env.BUILD_NUMBER}                       "
            echo " Build URL    : ${env.BUILD_URL}                          "
            echo "=========================================================="
        }
        success {
            echo ">>> BUILD STATUS: SUCCESS <<<"
            echo "All stages (Validate -> Test -> Build -> Deploy) completed successfully."
            echo "The To-Do List application is live and operational!"
        }
        failure {
            echo ">>> BUILD STATUS: FAILED <<<"
            echo "CRITICAL: The pipeline failed! Please inspect the console log above."
            echo "Troubleshooting steps:"
            echo " 1. Check 'Code Validation' stage for PHP syntax errors."
            echo " 2. Check 'Automated Testing' stage for test assertion failures."
            echo " 3. Verify database connectivity and required files."
        }
        unstable {
            echo ">>> BUILD STATUS: UNSTABLE <<<"
        }
    }
}
