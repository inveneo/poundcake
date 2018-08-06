<dt>Addrpool Addresses</dt>
    <?php
        if (!isset($ip_addresses[0])) {
            echo "<dd>None</dd>";
        } else {
            $c = count($ip_addresses[0]);
            //echo "<dd>";
            foreach ($ip_addresses as $subnet) {
                //echo $this->Html->link($contact['first_name']." ".$contact['last_name']), array(
                echo "<dd>";
                echo $subnet['addrpool_subnet']['name'];
                echo '&nbsp;'.$subnet['addrpool_subnet']['base'];
                echo '/'.$subnet['addrpool_subnet']['slash'];
                
                echo "</dd>";
            }
            //echo "</UL>";
        }
    ?>
</P>