<!DOCTYPE html>
<html>
    <head>
        <title>Gravity-Test</title>
        <script src="../resources/assets/js/codemirror.js"></script>
        <script src="../resources/assets/js/jquery-1.12.1.min.js"></script>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link href="../resources/assets/css/codemirror.css" rel="stylesheet">
        <link href="../resources/assets/css/elegant.css" rel="stylesheet">
        <link href="../resources/assets/css/style.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <h1>Prueba Gravility</h1>
        </header>
        <nav>
            <a class="button green" id="run">
                <i class="material-icons">play_arrow</i>
                <label>Run</label>
            </a>
        </nav>
        <div class="container">
            <section>
                <textarea id="code"></textarea>
            </section>
            <section>
                <code></code>
            </section>
        </div>
        <footer>
            <table>
                <thead>
                    <tr>
                        <th width="70">line</th>
                        <th width="70">code</th>
                        <th>message</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </footer>
        <script>
            var code = document.getElementById("code");
            var run = document.getElementById("run");
            var cm = CodeMirror.fromTextArea(code, {
                mode: 'javascript',
                lineNumbers: true,
                lineWiseCopyCut: false
            });
            run.addEventListener("click", function () {
                cm.save();
                $("footer table td").remove();
                $.ajax({
                    url: 'run',
                    method: 'post',
                    type: 'json',
                    data: {
                        text: cm.getTextArea().value
                    },
                    success: function (data) {
                        $("section code").html("");
                        if (data.error) {
                            var tr = $("<tr></tr>");
                            $("<td class='num'>" + data.error.line + "</td>").appendTo(tr);
                            $("<td class='num'>" + data.error.code + "</td>").appendTo(tr);
                            $("<td>" + data.error.message + "</td>").appendTo(tr);
                            tr.appendTo("footer table tbody");
                        } else {
                            var lines = data.result.split("\n");
                            for (var i = 0; i < lines.length; i++){
                                $("<div>" + lines[i] + "</div>").appendTo("section code");
                            }
                            
                        }
                    }
                });
                return false;
            });
        </script>
    </body>
</html>
