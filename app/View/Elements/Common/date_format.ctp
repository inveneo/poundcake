
<H4><B>Date Format</B></H4>
<p>
    The datetime format field should be the notation from PHP's <a href="http://php.net/manual/en/function.date.php">date</a> function.
</p>
<p>
    For example, if the real date were <?php echo date("F j, Y"); ?>...
<ul>
    <LI>
        Entering "m/d/Y" would yield the date as: <?php echo date("m/d/Y"); ?>
    </LI>
    <LI>
        Entering "m.d.y" would yield the date as: <?php echo date("m.d.y"); ?>
    </LI>
    <LI>
        Entering "Y-m-d" would yield the date as: <?php echo date("Y-m-d"); ?>
    </LI>
    <LI>
        Sophisticated date and time formats are not tested!
    </LI>
</ul>