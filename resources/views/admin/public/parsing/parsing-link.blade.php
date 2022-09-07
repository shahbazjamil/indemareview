<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Parsing</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        .bookmarklet-page{
            width: 900px;
            margin: 0 auto;
            padding: 10px;
        }
        .p-button{
            border: 1px solid green;
            border-radius: 4px;
            padding: 4px 6px;
            color: white;
            background-color: darkcyan;
            text-decoration: none;
        }

    </style>
</head>

<body id="bookmarklet" data-role="designer" data-system-flash="">

<main class="main">
    <div class="bookmarklet-page">
        <h1 class="page-header">Save images from any website directly to your client projects.</h1>
        <div class="bookmarklet-page__block content-block">
            <h2 class="bookmarklet-page__content-header">Follow these steps to Install the DesignFiles Browser
                Clipper.</h2>
            <p>
                <span class="">1. Make sure your bookmarks bar is open in your browser.</span><br>
                <span class="">- Open Chrome <br></span>
                <span class="">- Select the View menu <br></span>
                <span class="">- Find Show Favorites Bar <br></span>
                <span class="">- Enable it <br></span>
            </p>
            <p>
                2. Drag this button
                <a class="p-button"
                   href="javascript:if(document.readyState==='complete'){var element = document.createElement('input');element.type = 'hidden';element.value = '{{$uuid}}';element.id = 'idemia_uuid'; with(document)(body.appendChild(createElement('script')).src='https://app.indema.co/js/parsing/plugins.js', body.appendChild(element))}else{alert('Please wait until the page loads.')}">Add
                    to Indemia</a>
                to your bookmarks bar. You're done!
            </p>
        </div>
    </div>
</main>
</body>
</html>
