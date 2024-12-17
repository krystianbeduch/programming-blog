<!DOCTYPE html><html lang="en"><head>
    <meta charset="utf-8">
    <title>jQuery UI Checkboxradio - No Icons</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<!--    <link rel="stylesheet" href="/resources/demos/style.css">-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "input" ).checkboxradio({
                icon: false
            });
        } );
    </script>
</head>
<body>
<div class="widget">
    <h1>Checkbox and radio button widgets</h1>

    <h2>Radio Group</h2>
    <fieldset>
        <legend>Select a Location: </legend>
        <label for="radio-1">New York</label>
        <input type="radio" name="radio-1" id="radio-1">
        <label for="radio-2">Paris</label>
        <input type="radio" name="radio-1" id="radio-2">
        <label for="radio-3">London</label>
        <input type="radio" name="radio-1" id="radio-3">
    </fieldset>
</div>




</body></html>