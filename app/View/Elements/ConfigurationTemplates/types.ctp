<?php
    echo $this->Form->label(null,'Applies To');
    $n = 0;
    foreach ( $options as $key => $value ) {
        echo '<label class="radio">';
        echo '<input type="radio" name="data[ConfigurationTemplate][type]" value="'.$key.'"';
        if ( $n == $selected ) {
            echo ' checked';
        }
        echo '>';
        echo $value;
        echo '</label>';
        $n++;
    }
?>