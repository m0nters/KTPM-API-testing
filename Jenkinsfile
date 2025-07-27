pipeline {
    agent any

    environment {
        SPRINT_FOLDER = 'sprint5-with-bugs'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        stage('Start Docker') {
            steps {
                sh 'docker compose down || true'
                sh 'docker compose up -d'
                sh 'sleep 30'
            }
        }
        stage('Install Dependencies') {
            steps {
                sh 'docker compose exec -T laravel-api composer install --no-progress --prefer-dist --no-interaction'
            }
        }
        stage('Prepare Environment') {
            steps {
                sh '''
                    docker compose exec -T laravel-api cp .env.example .env || true
                    docker compose exec -T laravel-api php artisan key:generate
                '''
            }
        }
        stage('Migrate Database') {
            steps {
                sh 'docker compose exec -T laravel-api php artisan migrate --force'
            }
        }
        stage('Run Unit Tests') {
            steps {
                sh 'docker compose exec -T laravel-api php artisan test --env=testing --testdox'
            }
        }
    }
}