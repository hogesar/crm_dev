<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>

        h1, h2, h3, h4, h5, h6 {
            page-break-after: avoid;
        }

        body {
            background: none;
        }



        .pageA4 {
/*
            width: 21cm;
            height: 29.7cm;
            box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
            padding: 1cm 2cm 1cm 2cm;

*/
            font-family : tahoma, verdana, arial, sans-serif;
            margin: 0 auto;
            margin-bottom: 0.5cm;
            display: block;
            background: none;
        }

/*
        @media screen {
            body, .pageA4 {
                height: 29.7cm;
                box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);
            }
        }
*/
         @media print {
            body, .pageA4 {
                box-shadow: 0;
                margin: 0 auto;
            }
        }


    </style>
</head>
<body>
<div class="pageA4">
    @yield('content')
</div>
</body>
</html>