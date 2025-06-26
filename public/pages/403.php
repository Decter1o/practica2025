<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Доступ запрещен</title>
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #db2828;
            margin-bottom: 5rem;
        }
        .error-message {
            font-size: 1.5rem;
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">Доступ запрещен</div>
        <p class="ui text">У вас недостаточно прав для доступа к этой странице.</p>
        <div class="ui buttons">
            <a href="javascript:history.back()" class="ui labeled icon button">
                <i class="left arrow icon"></i>
                Вернуться назад
            </a>
            <a href="/" class="ui primary labeled icon button">
                <i class="home icon"></i>
                На главную
            </a>
        </div>
    </div>
    <script src="/public/css/Semantic/semantic.min.js"></script>
</body>
</html>