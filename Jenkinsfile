pipeline {
    agent any

    triggers {
        pollSCM('* * * * *') // Hoặc cấu hình webhook GitHub
    }

    options {
        disableConcurrentBuilds()
    }

    environment {
        DISABLE_LOGGING = 'true'
        SPRINT_FOLDER = 'sprint5-with-bugs'
    }

    stages {
        stage('Test') {
            steps {
                script {
                    echo "Start Test Stage"
                }
            }
        }

        stage('Deploy') {
            when {
                branch 'main'
                // Hoặc dùng script để kiểm tra branch develop / feature/*
            }
            steps {
                script {
                    echo "Start Deploy Stage"
                }
            }
        }
    }
}
