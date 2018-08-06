<!-- <a href="javascript:window.history.back()">Back</a> -->
<?php        
    $breadcrumbs = CakeSession::read('breadcrumbs');
    if (!isset($breadcrumbs)) {
        $breadcrumbs = array();
    }

    $controller = $this->name;
    $view = $this->action;
    // don't show the breadcrumbs if we're on the login page
    if ($view != 'login') {
        $label = $controller.'/'.$view;
        $url = $controller.'/'.$view;
        $link = '';

        $params = '';
        if (sizeof($this->params['pass']) > 0)
            $params = $this->params['pass'][0];

//            echo '<pre>';
//            echo $label;
//            echo $url;
//            echo '</pre>';

        $link = $this->Html->link(
            $label,
            array(
                'controller' => $controller,
                'action' => $view,
                $params
            ));

//            echo '<pre>';
//            //print_r($this->params['pass']);
//            echo "LABEL:  ".$label.'<br>';
//            echo "URL:  ".$url.'<br>';
//            //echo $length;
//            echo '</pre>';

        //echo $link;
        array_unshift($breadcrumbs, $link);
        
        $i = 0;
        $max = 6;
        if (sizeof($breadcrumbs) < $max)
            $max = sizeof($breadcrumbs);                
        //echo "Max is $max<BR>";

        $breadcrumbs = array_slice($breadcrumbs, 0, $max);
        // http://twitter.github.com/bootstrap/components.html#breadcrumbs
        
        echo '<i class="icon-list"></i>&nbsp;<strong>Page History</strong><BR>';
        echo '<ul class="poundcake-breadcrumb">';
        echo '<span class="active"></span>';
        echo '</LI>&nbsp;&nbsp;&nbsp;';
        
        foreach ($breadcrumbs as $bc ) {
            echo '<LI>';                    
            if ($i == 0) {
                // the current page shouldn't be a hyperlink
                // though we want to keep it as-such in the array
                // so just strip the tags here
                //echo '<i class="icon-hand-right"></i>';
                echo strip_tags($bc);
                // maybe it's just me but I could swear that divider has an
                // extra space over active, so just pad it manually here
                //echo '<span class="active">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>';
                echo '<span class="active">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>';
            } else {
                echo $bc;
                //echo '<i class="icon-list-alt"></i>';
                echo '<span class="divider">';
                // don't put a >> onto the last breadcrumb
                if ($i < $max - 1 )
                    echo '&nbsp;&raquo;&nbsp;';
                echo '</span>';
            }
            echo '</LI>';
            $i++;
        }
        echo "</ul>";
        CakeSession::write('breadcrumbs', $breadcrumbs);
    }
?>