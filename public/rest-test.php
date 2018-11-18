<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Teste</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>
<script>
    $(document).ready(
        function () {
            /* CREATE */
            $.ajax({
                type: 'POST',
                contentType: "application/json",
                url: "/users",
                data: JSON.stringify({
                    name : "Jilles Moraes Cardoso",
                    email : "jillesmc@gmail.com",
                    phone : "35 99999-9999",
                    password : "basket16"
                }),
                success: function( result ) {
                    console.log(result);
                },
                complete: function (xhr, textStatus) {
                    console.log(xhr.status);
                }
            });


            /* UPDATE
            * pegar o user_id e colocar no localStorage */
            // let user_id = 5;
            // $.ajax({
            //     type: 'PUT',
            //     contentType: "application/json",
            //     url: "/users/"+user_id,
            //     data: JSON.stringify({
            //         name : "Jilles Moraes Cardoso",
            //         email : "jillesmc@gmail.com",
            //         phone : "35 99999-9999",
            //         password : "basket16"
            //     }),
            //     success: function( result ) {
            //         console.log(result);
            //     },
            //     complete: function (xhr, textStatus) {
            //         console.log(xhr.status);
            //     }
            // });
        }
    );
</script>
</body>

</html>