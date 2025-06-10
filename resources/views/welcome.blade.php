<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Registro de Voluntariado</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .buttons-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .btn {
            display: inline-block;
            padding: 16px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6);
        }

        .btn-success {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #2c3e50;
            box-shadow: 0 4px 15px rgba(168, 237, 234, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(168, 237, 234, 0.6);
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #95a5a6;
        }

        @media (max-width: 600px) {
            .container {
                padding: 40px 20px;
            }
            
            .title {
                font-size: 24px;
            }
            
            .btn {
                padding: 14px 28px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Sistema de Registro de Voluntariado</h1>
        <p class="subtitle">Gestione voluntarios y registre actividades de manera eficiente</p>
        
        <div class="buttons-container">
            <a href="{{ route('voluntarios.index') }}" class="btn btn-primary">
                👥 Gestionar Voluntarios
            </a>
            
            <a href="{{ route('registros.index') }}" class="btn btn-secondary">
                📋 Ver Registros
            </a>
            
            <a href="{{ route('registros.formulario') }}" class="btn btn-success">
                📝 Formulario
            </a>
        </div>
        
        <div class="footer">
            © {{ date('Y') }} Sistema de Registro de Voluntariado
        </div>
    </div>
</body>
</html>
        