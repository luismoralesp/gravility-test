<!DOCTYPE html>
<html>
    <head>
        <title>Gravity-Test</title>
    </head>
    <?php
    $encrypter = app('Illuminate\Encryption\Encrypter');
    $encrypted_token = $encrypter->encrypt(csrf_token());
    ?>
    <body>
        <div class="container">
            <div class="content">
                <form action="run" method="post">
                    
                    <textarea>
                        
                    </textarea>
                    <button>Enviar</button>
                </form>
            </div>
        </div>
    </body>
</html>
