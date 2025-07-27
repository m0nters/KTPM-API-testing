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
        NODE_VERSION = '22.2.0'
        PHP_VERSION = '8.3'
        
        // Environment detection based on branch
        ENVIRONMENT = "${env.BRANCH_NAME == 'main' ? 'production' : (env.BRANCH_NAME == 'develop' ? 'qa' : 'staging')}"
        VPS_HOST = "${env.BRANCH_NAME == 'main' ? 'prod-vps.example.com' : 'qa-vps.example.com'}"
        
        // Demo mode - using fake credentials (no actual deployment)
        DEMO_MODE = 'true'
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
                            echo "🎭 DEMO MODE: Simulating Docker setup and API tests..."
                            echo "🐳 [SIMULATED] Starting containers..."
                            echo "   → docker compose -f docker-compose.yml up -d"
                            sleep(2)
                            
                            echo "⏳ [SIMULATED] Waiting for containers to be ready..."
                            echo "   → Simulating 60 second wait..."
                            sleep(3)
                            
                            echo "🌱 [SIMULATED] Creating & seeding database..."
                            echo "   → docker compose exec -T laravel-api php artisan migrate:refresh --seed"
                            sleep(1)
                            
                            echo "🔍 [SIMULATED] Testing API endpoints..."
                            echo "   → GET http://localhost:8091/status"
                            echo "   ✅ Status: 200 OK - API is running"
                            sleep(1)
                            
                            echo "   → POST http://localhost:8091/users/login"
                            echo "   ✅ Login: 200 OK - Authentication successful"
                            echo "🎉 Docker setup and API tests completed successfully!"
                        }
                    }
                }
                
                stage('Laravel Unit Tests') {
                    steps {
                        script {
                            echo "🎭 DEMO MODE: Simulating Laravel Unit Tests..."
                            echo "🐘 [SIMULATED] Setting up PHP ${PHP_VERSION}..."
                            sleep(1)
                            
                            echo "📦 [SIMULATED] Installing API Dependencies..."
                            echo "   → composer install --no-progress --prefer-dist --no-interaction"
                            sleep(2)
                            
                            echo "🧪 [SIMULATED] Running Laravel Unit Tests..."
                            echo "   → Setting up test environment (.env.testing)"
                            echo "   → php artisan config:clear"
                            echo "   → php artisan test --env=testing --testdox"
                            sleep(2)
                            
                            echo "✅ Feature Tests:"
                            echo "   ✓ User can register successfully"
                            echo "   ✓ User can login with valid credentials"
                            echo "   ✓ API returns proper error codes"
                            echo "   ⚠️ Some tests failed (expected for 'with-bugs' version)"
                            echo "🎉 Laravel tests completed!"
                        }
                    }
                }
                
                stage('UI Tests Setup') {
                    steps {
                        script {
                            echo "🎭 DEMO MODE: Simulating UI Tests Setup..."
                            echo "⚙️ [SIMULATED] Installing Node.js ${NODE_VERSION}..."
                            sleep(1)
                            
                            echo "📦 [SIMULATED] Installing UI Dependencies..."
                            echo "   → Cleaning previous installation..."
                            echo "   → npm ci --legacy-peer-deps"
                            echo "   ✅ npm ci succeeded"
                            sleep(2)
                            
                            echo "   → Detecting Playwright version: 1.40.0"
                            echo "🏗 [SIMULATED] Installing Playwright browsers..."
                            echo "   → npx playwright install --with-deps"
                            echo "   ✅ Chromium, Firefox, WebKit browsers installed"
                            sleep(2)
                            
                            echo "🎭 [SIMULATED] Running UI Tests..."
                            echo "   ✓ Login page loads correctly"
                            echo "   ✓ User can navigate to product catalog"
                            echo "   ✓ Shopping cart functionality works"
                            echo "   ⚠️ Some UI tests may have issues (expected for demo)"
                            echo "🎉 UI tests setup completed!"
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
                    echo "🎭 DEMO MODE: Simulating deployment preparation..."
                    echo "🎯 Environment: ${env.ENVIRONMENT}"
                    echo "🖥️ Target VPS: ${env.VPS_HOST}"
                    echo "🌿 Branch: ${env.BRANCH_NAME}"
                    
                    echo "🐘 [SIMULATED] Setting up PHP..."
                    echo "   ✅ PHP ${PHP_VERSION} is ready"
                    sleep(1)
                    
                    echo "⚙️ [SIMULATED] Setting up Node.js..."
                    echo "   ✅ Node.js ${NODE_VERSION} is ready"
                    sleep(1)
                    
                    echo "📦 [SIMULATED] Installing Dependencies (dev)..."
                    echo "   → composer update --no-progress --prefer-dist"
                    echo "   ✅ Development dependencies installed"
                    sleep(1)
                    
                    echo "📦 [SIMULATED] Installing Dependencies (production)..."
                    echo "   → composer update --no-dev --prefer-dist --optimize-autoloader"
                    echo "   ✅ Production dependencies optimized"
                    sleep(1)
                    
                    echo "⚙️ [SIMULATED] Creating environment file..."
                    echo "   → Processing .env template with demo credentials"
                    echo "   ✅ Environment file created successfully"
                    echo "🎉 Deployment preparation completed!"
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
                            echo "🎭 DEMO MODE: Simulating Production Deployment..."
                            echo "🏭 PRODUCTION DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ [SIMULATED] Connecting to Production VPS: prod-vps.example.com"
                            echo "🔐 [SIMULATED] Using production SSH key"
                            echo "🌿 Deploying from main branch"
                            echo "⚙️ Environment: production"
                            echo "🗄️ Database: production_db"
                            echo "🚀 [SIMULATED] Starting production deployment..."
                            sleep(3)
                            echo "✅ [SIMULATED] Code pulled from main branch"
                            echo "✅ [SIMULATED] Dependencies installed"
                            echo "✅ [SIMULATED] Database migrations applied"
                            echo "✅ [SIMULATED] Cache cleared and optimized"
                            echo "✅ [SIMULATED] Production services restarted"
                            echo "🎉 PRODUCTION DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🎭 DEMO MODE: Simulating production post-deployment tasks..."
                                echo "🔧 [SIMULATED] Running production post-deployment tasks..."
                                echo "✅ [SIMULATED] SSL certificates verified"
                                echo "✅ [SIMULATED] CDN cache purged"
                                echo "✅ [SIMULATED] Monitoring alerts configured"
                                echo "✅ [SIMULATED] Backup jobs scheduled"
                                echo "📧 [SIMULATED] Production deployment notification sent"
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
                            echo "🎭 DEMO MODE: Simulating QA Deployment..."
                            echo "🧪 QA DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ [SIMULATED] Connecting to QA VPS: qa-vps.example.com"
                            echo "🔐 [SIMULATED] Using QA SSH key"
                            echo "🌿 Deploying from develop branch"
                            echo "⚙️ Environment: qa"
                            echo "🗄️ Database: qa_db"
                            echo "🧪 [SIMULATED] Starting QA deployment..."
                            sleep(2)
                            echo "✅ [SIMULATED] Code pulled from develop branch"
                            echo "✅ [SIMULATED] Dependencies installed"
                            echo "✅ [SIMULATED] Test database seeded"
                            echo "✅ [SIMULATED] Debug mode enabled"
                            echo "✅ [SIMULATED] QA services restarted"
                            echo "🎉 QA DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🎭 DEMO MODE: Simulating QA post-deployment tasks..."
                                echo "🔧 [SIMULATED] Running QA post-deployment tasks..."
                                echo "✅ [SIMULATED] Test data populated"
                                echo "✅ [SIMULATED] Debug tools enabled"
                                echo "✅ [SIMULATED] Test reports configured"
                                echo "🧪 [SIMULATED] Smoke tests initiated"
                                echo "📧 [SIMULATED] QA deployment notification sent"
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
                            echo "🎭 DEMO MODE: Simulating Dev Deployment..."
                            echo "👨‍💻 DEV DEPLOYMENT"
                            echo "=========================================="
                            echo "🖥️ [SIMULATED] Connecting to Dev VPS: dev-vps.example.com"
                            echo "🔐 [SIMULATED] Using Dev SSH key"
                            echo "🌿 Deploying from ${env.BRANCH_NAME} branch"
                            echo "⚙️ Environment: dev"
                            echo "🗄️ Database: dev_db"
                            echo "👨‍💻 [SIMULATED] Starting Dev deployment..."
                            sleep(1)
                            echo "✅ [SIMULATED] Code pulled from ${env.BRANCH_NAME} branch"
                            echo "✅ [SIMULATED] Dependencies installed"
                            echo "✅ [SIMULATED] Dev database seeded"
                            echo "✅ [SIMULATED] Debug mode enabled"
                            echo "✅ [SIMULATED] Dev services restarted"
                            echo "🎉 DEV DEPLOYMENT COMPLETED!"
                        }
                    }
                    post {
                        success {
                            script {
                                echo "🎭 DEMO MODE: Simulating Dev post-deployment tasks..."
                                echo "🔧 [SIMULATED] Running Dev post-deployment tasks..."
                                echo "✅ [SIMULATED] Dev test data populated"
                                echo "✅ [SIMULATED] Dev debug tools enabled"
                                echo "✅ [SIMULATED] Dev test reports configured"
                                echo "👨‍💻 [SIMULATED] Dev smoke tests initiated"
                                echo "📧 [SIMULATED] Dev deployment notification sent"
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
                echo "🎭 DEMO MODE SUMMARY"
                echo "📊 DEPLOYMENT SUMMARY"
                echo "=========================================="
                echo "🌍 Environment: ${env.ENVIRONMENT}"
                echo "🖥️ Target Server: ${env.VPS_HOST}"
                echo "🌿 Branch: ${env.BRANCH_NAME}"
                echo "⏰ Deployment Time: ${new Date()}"
                echo "👤 Triggered by: ${env.BUILD_USER ?: 'System'}"
                echo "🔗 Commit: ${env.GIT_COMMIT ?: 'demo-commit-hash'}"
                echo "✅ Deployment Status: ${currentBuild.currentResult}"
                echo "🎭 Mode: DEMO (No actual deployments performed)"
                echo "=========================================="
                
                if (env.ENVIRONMENT == 'production') {
                    echo "🌐 [DEMO] Production URL: https://prod.practicesoftwaretesting.com"
                } else if (env.ENVIRONMENT == 'qa') {
                    echo "🌐 [DEMO] QA URL: https://qa.practicesoftwaretesting.com"
                } else {
                    echo "🌐 [DEMO] Dev URL: https://dev.practicesoftwaretesting.com"
                }
            }
        }
        success {
            echo "🎉 Demo pipeline completed successfully!"
            echo "🎭 All simulated deployments passed!"
        }
        failure {
            echo "❌ Demo pipeline failed!"
            echo "🎭 Check the simulated steps above"
        }
        cleanup {
            // Clean up Docker containers (simulated)
            echo "🎭 [SIMULATED] Cleaning up Docker containers..."
            echo "   → docker compose down"
            echo "✅ Demo cleanup completed"
        }
    }
}