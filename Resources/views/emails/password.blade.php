<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Recuperar Contraseña</title>
    <style type="text/css">
        body { margin: 0; padding: 0; }

        @media only screen and (max-width: 660px){
            body{border:1px solid red;}
            table.container{width:90% !important; margin:20px auto !important;}
        }

        /* overrides for screen sizes under 510 */
        @media only screen and (max-width: 510px) {
            body{border:1px solid green;}
            table.container{width:90% !important; margin:20px auto !important;}

        }
    </style>
</head>
<body bgcolor="#fefefe">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fefefe" style="margin-top:50px">
    <tr>
        <td>
            <table class="container" width="450" align="center" border="0"  cellspacing="0" cellpadding="0" style="padding: 40px 30px; text-align: center; ">
                <tr>
                    <td valign="middle" bgcolor="#ffffff"  >

                    </td>
                </tr>
                <tr>
                    <td>
                        <h1 class="texto1" style="font-family: Arial, Helvetica, sans-serif; font-size:22px; font-weight: bold; color:#14a9e1; text-transform: uppercase; margin:30px 10px 10px 10px; ">Recuperación de contraseña</h1>
                        <span class="texto2" style="font-family: Arial, Helvetica, sans-serif; font-size:18px;  color:#636363;  font-weight: normal; margin: 0px 15px 0 15px; display:block;">
                            Parece que ha solicitado restaurar su contraseña, por favor haga click en el enlace a continuación<br />
                            <a href="{{ url('password/reset/'.$token) }}">{{ url('password/reset/'.$token) }}</a><br /><br />
                            Gracias.
                        </span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>