<!--<button type="button" class="close" data-dismiss="alert">&times;</button>-->
<dl class="dl-horizontal">
    <dt>Distance</dt>
    <dd><?php echo sprintf("%01.2f",$remote[0]); ?> Km</dd>

    <dt>True Azimuth</dt>
    <dd><? echo sprintf("%01.2f",$remote[1])?>°</dd>

    <dt>Magnetic Azimuth</dt>
    <dd><? echo sprintf("%01.2f",$remote[2])?>°</dd>
</dl>