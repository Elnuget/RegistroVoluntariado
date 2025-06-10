<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Registro de Voluntariado' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Google Sans', 'Roboto', Arial, sans-serif;
            background-color: #f1f3f4;
            color: #202124;
            line-height: 1.6;
        }

        .form-container {
            max-width: 640px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 24px;
            color: white;
            text-align: center;
        }

        .form-title {
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 8px;
        }

        .form-description {
            font-size: 14px;
            opacity: 0.9;
        }

        .form-body {
            padding: 24px;
        }

        .form-question {
            margin-bottom: 32px;
        }

        .question-label {
            display: block;
            font-size: 16px;
            font-weight: 400;
            color: #202124;
            margin-bottom: 8px;
            position: relative;
        }

        .required::after {
            content: " *";
            color: #d93025;
        }

        .form-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #dadce0;
            padding: 8px 0;
            font-size: 16px;
            font-family: inherit;
            background: transparent;
            transition: border-color 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-bottom: 2px solid #1a73e8;
        }

        .form-select {
            width: 100%;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 12px;
            font-size: 16px;
            font-family: inherit;
            background: white;
            outline: none;
            transition: border-color 0.2s;
        }
        
        .form-select:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 1px #1a73e8;
        }

        .select-container {
            position: relative;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dadce0;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.selected {
            background-color: #e8f0fe;
            color: #1a73e8;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .timezone-info {
            font-size: 11px;
            color: #5f6368;
            margin-top: 4px;
            font-style: italic;
            padding: 4px 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #34a853;
        }

        .submit-section {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e8eaed;
        }

        .submit-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 120px;
        }

        .submit-btn:hover {
            background: #1557b0;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
        }

        .submit-btn:active {
            background: #1557b0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 16px;
            margin-bottom: 24px;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #fce8e6;
            border: 1px solid #fad2cf;
            color: #d93025;
        }

        .alert-success {
            background-color: #e6f4ea;
            border: 1px solid #ceead6;
            color: #137333;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .form-footer {
            padding: 16px 24px;
            background: #f8f9fa;
            font-size: 12px;
            color: #5f6368;
            text-align: center;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 20px 16px;
            }
            
            .form-header {
                padding: 20px 16px;
            }
            
            .form-body {
                padding: 20px 16px;
            }
            
            .form-title {
                font-size: 28px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="form-container">
        @yield('content')
    </div>
    
    @stack('scripts')
</body>
</html>
