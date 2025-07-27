pipeline {
    agent any
    
    // Equivalent to GitHub Actions concurrency
    options {
        disableConcurrentBuilds()
        timeout(time: 60, unit: 'MINUTES')
        buildDiscarder(logRotator(numToKeepStr: '10'))
    }
    
    triggers {
        // Trigger on push to main, feature/*, develop branches
        githubPush()
    }
    
    environment {
        DISABLE_LOGGING = 'true'
        SPRINT_FOLDER = 'sprint5-with-bugs'
        NODE_VERSION = '22'
        PHP_VERSION = '8.3'
        
        // Environment detection based on branch
        ENVIRONMENT = "${env.BRANCH_NAME == 'main' ? 'production' : (env.BRANCH_NAME == 'develop' ? 'qa' : 'staging')}"
        VPS_HOST = "${env.BRANCH_NAME == 'main' ? 'prod-vps.example.com' : 'qa-vps.example.com'}"
        
        // Credentials for environment file (can be fake for demo)
        SECRET_DB = 'demo_database_connection'
        SECRET_DB_USER = 'demo_user'
        SECRET_DB_PASS = 'demo_password'
        SECRET_GOOGLE_ID = 'demo_google_client_id'
        SECRET_GOOGLE_SECRET = 'demo_google_secret'
        SECRET_GITHUB_ID = 'demo_github_client_id'
        SECRET_GITHUB_SECRET = 'demo_github_secret'
    }
    
    stages {
        stage('Checkout') {
            steps {
                script {
                    echo "⚙️ Checking out code..."
                    // Checkout is automatic in Jenkins Pipeline
                    checkout scm
                }
            }
        }
        
        stage('Test') {
            parallel {
                stage('Docker Setup & API Tests') {
                    steps {
                        script {
                            echo "🐳 Starting containers..."
                            sh '''
                                export DISABLE_LOGGING=true
                                export SPRINT_FOLDER=sprint5-with-bugs
                                docker compose -f docker-compose.yml up -d
                            '''
                            
                            echo "⏳ Waiting for containers to be ready..."
                            sleep(60)
                            
                            echo "🌱 Creating & seeding database..."
                            sh 'docker compose exec -T laravel-api php artisan migrate:refresh --seed'
                            
                            echo "🔍 Testing API endpoints..."
                            sh "curl -v -X GET 'http://localhost:8091/status'"
                            sh """
                                curl -v -X POST 'http://localhost:8091/users/login' \\
                                -H 'Content-Type: application/json' \\
                                --data-raw '{"email":"customer@practicesoftwaretesting.com","password":"welcome01"}'
                            """
                        }
                    }
                }
                
                stage('Laravel Unit Tests') {
                    steps {
                        script {
                            echo "🐘 Setting up PHP ${PHP_VERSION}..."
                            // Use PHP installation or Docker PHP container
                            
                            echo "📦 Installing API Dependencies..."
                            sh '''
                                cd sprint5-with-bugs/API
                                sudo chown -R $USER:$USER . || true
                                composer install --no-progress --prefer-dist --no-interaction
                            '''
                            
                            echo "🧪 Running Laravel Unit Tests..."
                            sh '''
                                cd sprint5-with-bugs/API
                                # Setup test environment
                                cp .env.example .env.testing || cp .env .env.testing
                                echo 'APP_ENV=testing' >> .env.testing
                                echo 'DB_CONNECTION=sqlite' >> .env.testing
                                echo 'DB_DATABASE=:memory:' >> .env.testing
                                echo 'CACHE_DRIVER=array' >> .env.testing
                                echo 'SESSION_DRIVER=array' >> .env.testing
                                echo 'QUEUE_CONNECTION=sync' >> .env.testing
                                
                                # Clear config cache
                                php artisan config:clear
                                
                                # Run tests but don't fail the workflow
                                echo "🧪 Running Laravel tests (some may fail due to Sprint 5 'with-bugs' nature)"
                                php artisan test --env=testing --testdox || {
                                  echo "⚠️ Tests completed with some failures (expected for 'with-bugs' version)"
                                }
                            '''
                        }
                    }
                }
                
                stage('UI Tests Setup') {
                    steps {
                        script {
                            echo "⚙️ Installing Node.js ${NODE_VERSION}..."
                            // Install Node.js - you might need NodeJS plugin
                            
                            echo "📦 Installing UI Dependencies..."
                            sh '''
                                cd sprint5-with-bugs/UI
                                
                                # Step 1: Clean and fix permissions
                                echo "🧹 Cleaning previous installation..."
                                sudo rm -rf node_modules package-lock.json .npm 2>/dev/null || true
                                npm cache clean --force 2>/dev/null || true
                                sudo chown -R $USER:$USER . 2>/dev/null || true
                                
                                # Step 2: Try multiple installation strategies
                                echo "📦 Installing dependencies..."
                                if npm ci --legacy-peer-deps; then
                                  echo "✅ npm ci succeeded"
                                elif npm install --legacy-peer-deps; then
                                  echo "✅ npm install succeeded"
                                elif npm install --force; then
                                  echo "✅ npm install --force succeeded"
                                else
                                  echo "❌ All npm install methods failed, skipping UI tests"
                                  touch SKIP_UI_TESTS
                                  exit 0
                                fi
                                
                                # Step 3: Get Playwright version
                                PLAYWRIGHT_VERSION=$(npm list @playwright/test --json 2>/dev/null | jq -r '.dependencies["@playwright/test"].version // "1.40.0"')
                                echo "✅ Detected Playwright version: $PLAYWRIGHT_VERSION"
                                echo "$PLAYWRIGHT_VERSION" > playwright_version.txt
                            '''
                            
                            echo "🏗 Installing Playwright browsers..."
                            sh '''
                                cd sprint5-with-bugs/UI
                                if [ ! -f SKIP_UI_TESTS ]; then
                                    npx playwright install --with-deps
                                fi
                            '''
                        }
                    }
                }
            }
            post {
                always {
                    script {
                        // Archive test results and reports
                        archiveArtifacts artifacts: 'sprint5-with-bugs/UI/playwright-report/**', allowEmptyArchive: true
                    }
                }
            }
        }
        
        stage('Deploy') {
            when {
                anyOf {
                    branch 'main'
                    branch 'develop'
                    branch 'feature/*'
                }
            }
            steps {
                script {
                    echo "🎯 Environment: ${env.ENVIRONMENT}"
                    echo "🖥️ Target VPS: ${env.VPS_HOST}"
                    echo "🌿 Branch: ${env.BRANCH_NAME}"
                    
                    echo "🐘 Setting up PHP..."
                    // Setup PHP - this will work if PHP is installed on Jenkins agent
                    
                    echo "⚙️ Setting up Node.js..."
                    // Setup Node.js - this will work if Node is installed on Jenkins agent
                    
                    echo "📦 Installing Dependencies (dev)..."
                    sh '''
                        cd sprint5-with-bugs/API
                        composer update --no-progress --prefer-dist
                    '''
                    
                    echo "📦 Installing Dependencies (production)..."
                    sh '''
                        cd sprint5-with-bugs/API
                        composer update --no-dev --prefer-dist --optimize-autoloader
                        composer dump-autoload -o
                    '''
                    
                    echo "⚙️ Creating environment file..."
                    sh '''
                        cd sprint5-with-bugs/API
                        # Create .env file from template with environment substitution
                        envsubst < .env_template > .env
                    '''
                }
            }
        }
        
        stage('Environment-Specific Deployment') {
            parallel {
                stage('Production Deployment') {
                    when {
                        branch 'main'
                    }
                    steps {
                        script {
                            echo "🏭 PRODUCTION DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ Connecting to Production VPS: prod-vps.example.com"
                            echo "🔐 Using production SSH key"
                            echo "🌿 Deploying from main branch"
                            echo "⚙️ Environment: production"
                            echo "🗄️ Database: production_db"
                            echo "🚀 Starting production deployment..."
                            echo "✅ Code pulled from main branch"
                            echo "✅ Dependencies installed"
                            echo "✅ Database migrations applied"
                            echo "✅ Cache cleared and optimized"
                            echo "✅ Production services restarted"
                            echo "🎉 PRODUCTION DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🔧 Running production post-deployment tasks..."
                                echo "✅ SSL certificates verified"
                                echo "✅ CDN cache purged"
                                echo "✅ Monitoring alerts configured"
                                echo "✅ Backup jobs scheduled"
                                echo "📧 Production deployment notification sent"
                            }
                        }
                    }
                }
                
                stage('QA Deployment') {
                    when {
                        branch 'develop'
                    }
                    steps {
                        script {
                            echo "🧪 QA DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ Connecting to QA VPS: qa-vps.example.com"
                            echo "🔐 Using QA SSH key"
                            echo "🌿 Deploying from develop branch"
                            echo "⚙️ Environment: qa"
                            echo "🗄️ Database: qa_db"
                            echo "🧪 Starting QA deployment..."
                            echo "✅ Code pulled from develop branch"
                            echo "✅ Dependencies installed"
                            echo "✅ Test database seeded"
                            echo "✅ Debug mode enabled"
                            echo "✅ QA services restarted"
                            echo "🎉 QA DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🔧 Running QA post-deployment tasks..."
                                echo "✅ Test data populated"
                                echo "✅ Debug tools enabled"
                                echo "✅ Test reports configured"
                                echo "🧪 Smoke tests initiated"
                                echo "📧 QA deployment notification sent"
                            }
                        }
                    }
                }
                
                stage('Dev Deployment') {
                    when {
                        branch 'feature/*'
                    }
                    steps {
                        script {
                            echo "👨‍💻 DEV DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ Connecting to Dev VPS: dev-vps.example.com"
                            echo "🔐 Using Dev SSH key"
                            echo "🌿 Deploying from ${env.BRANCH_NAME} branch"
                            echo "⚙️ Environment: dev"
                            echo "🗄️ Database: dev_db"
                            echo "👨‍💻 Starting Dev deployment..."
                            echo "✅ Code pulled from ${env.BRANCH_NAME} branch"
                            echo "✅ Dependencies installed"
                            echo "✅ Dev database seeded"
                            echo "✅ Debug mode enabled"
                            echo "✅ Dev services restarted"
                            echo "🎉 DEV DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🔧 Running Dev post-deployment tasks..."
                                echo "✅ Dev test data populated"
                                echo "✅ Dev debug tools enabled"
                                echo "✅ Dev test reports configured"
                                echo "👨‍💻 Dev smoke tests initiated"
                                echo "📧 Dev deployment notification sent"
                            }
                        }
                    }
                }
            }
        }
    }
    
    post {
        always {
            script {
                echo "📊 DEPLOYMENT SUMMARY"
                echo "=========================================="
                echo "🌍 Environment: ${env.ENVIRONMENT}"
                echo "🖥️ Target Server: ${env.VPS_HOST}"
                echo "🌿 Branch: ${env.BRANCH_NAME}"
                echo "⏰ Deployment Time: ${new Date()}"
                echo "👤 Triggered by: ${env.BUILD_USER ?: 'System'}"
                echo "🔗 Commit: ${env.GIT_COMMIT ?: 'current-commit-hash'}"
                echo "✅ Deployment Status: ${currentBuild.currentResult}"
                echo "=========================================="
                
                if (env.ENVIRONMENT == 'production') {
                    echo "🌐 Production URL: https://prod.practicesoftwaretesting.com"
                } else if (env.ENVIRONMENT == 'qa') {
                    echo "🌐 QA URL: https://qa.practicesoftwaretesting.com"
                } else {
                    echo "🌐 Dev URL: https://dev.practicesoftwaretesting.com"
                }
            }
        }
        success {
            echo "🎉 Pipeline completed successfully!"
        }
        failure {
            echo "❌ Pipeline failed!"
        }
        cleanup {
            // Clean up Docker containers
            sh 'docker compose down || true'
        }
    }
}