<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/Semantic/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="/semantic/dist/semantic.min.js" defer></script>
    <title>Вход</title>
</head>
<body>
    <div class="ui container" style="margin-top: 50px; max-width: 500px;">
    <h2 class="ui dividing header">Вход</h2>

    <form class="ui form" id="registerForm" method="post" action="/src/controllers/login_controller.php">

        <div class="field required">
            <label>Почта</label>
            <input type="email" name="email" placeholder="example@domain.com">
        </div>

        <div class="field required">
            <label>Пароль</label>
            <input type="password" name="password" id="password" placeholder="Введите пароль">
        </div>
        
        <button class="ui primary button" type="submit">Войти</button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="/semantic/dist/semantic.min.js"></script>
  <script>
    $('#registerForm')
      .form({
        fields: {
            email: {
                identifier: 'email',
                rules: [
                    { type: 'empty', prompt: 'Введите почту' },
                    { type: 'email', prompt: 'Введите корректную почту' }
                ]
            },
            password: {
                identifier: 'password',
                rules: [
                    { type: 'empty', prompt: 'Введите пароль' },
                    { type: 'minLength[8]', prompt: 'Пароль должен быть не менее 8 символов' },
                    { type: 'regExp[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$/]', 
                    prompt: 'Пароль должен содержать заглавные и строчные буквы, а также цифры' }
                ]
            }
        },
        inline: true,
        on: 'blur'
      });
  </script>
</body>
</html>