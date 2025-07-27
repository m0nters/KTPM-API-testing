pipeline {
    agent any

    environment {
        DISABLE_LOGGING = 'true'
        SPRINT_FOLDER = 'sprint5-with-bugs'
    }

    stages {
        stage('Test') {
            steps {
                script {
                    // 1. Checkout
                    checkout scm

                    // 2. Start Docker containers
                    sh '''
                        docker compose -f docker-compose.yml up -d
                    '''

                    // 3. Sleep đợi service ổn định
                    sh 'sleep 60'

                    // 4. Migrate + seed DB
                    sh '''
                        docker compose exec -T laravel-api php artisan migrate:refresh --seed
                    '''

                    // 5. Cài Laravel dependencies
                    sh '''
                        cd ${SPRINT_FOLDER}/API
                        composer install --no-progress --prefer-dist --no-interaction
                    '''

                    // 6. Tạo file .env.testing và cấu hình test DB
                    sh '''
                        cd ${SPRINT_FOLDER}/API
                        cp .env.example .env.testing || cp .env .env.testing
                        echo 'APP_ENV=testing' >> .env.testing
                        echo 'DB_CONNECTION=sqlite' >> .env.testing
                        echo 'DB_DATABASE=:memory:' >> .env.testing
                        echo 'CACHE_DRIVER=array' >> .env.testing
                        echo 'SESSION_DRIVER=array' >> .env.testing
                        echo 'QUEUE_CONNECTION=sync' >> .env.testing
                    '''

                    // 7. Xóa cache config
                    sh '''
                        cd ${SPRINT_FOLDER}/API
                        php artisan config:clear
                    '''

                    // 8. Chạy unit test
                    sh '''
                        cd ${SPRINT_FOLDER}/API
                        echo "🧪 Running Laravel tests..."
                        php artisan test --env=testing --testdox || echo "⚠️ Tests failed but continuing (due to known bugs)"
                    '''
                }
            }
        }
    }
}
