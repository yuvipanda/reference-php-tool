<?php

class NonDeterministicRunner {

    private $actions = array();

    public function registerAction($name, $priority, $action) {
        $this->actions[] = array( $name, $priority, $action );
    }

    public function run() {
        usort( $this->actions, function( $a, $b ) {
            $aval = $a[1];
            $bval = $b[1];
            return $aval - $bval;
        } );
        for ($i = 1; $i < count($this->actions); $i++) {
            $this->actions[$i][1] += $this->actions[$i-1][1];
        }
        $max = end($this->actions)[1];
        reset($this->actions); #PHPEEEE!

        $picked = rand(0, $max);

        $prev_value = 0;

        for ($i = 0; $i < count($this->actions); $i++ ) {
            if ( $picked >= $prev_value and $picked <= $this->actions[$i][1] ) {
                $this->actions[$i][2]();
                return $this->actions[$i];
            }
            $prev_value = $this->actions[$i][1];
        }

    }
}

$nd = new NonDeterministicRunner();
$nd->registerAction("be-sane", 98, function() {
    $big_array = array("b");
    for ($i = 1; $i < pow(2, 10); $i++) {
        $big_array[$i] = $big_array[$i - 1] . $big_array[0];
    }
    var_dump( $big_array );
    echo "Hi, I am the sane response!";
});
$nd->registerAction("be-forever", 1, function() {
    while (true) {
        echo "Forever, and ";
    }
});
$nd->registerAction("be-huge", 1, function() {
    $big_array = array("b");
    for ($i = 1; $i < pow(2, 64); $i++) {
        $big_array[$i] = $big_array[$i - 1] . $big_array[0];
    }
    var_dump($big_array);
});

$nd->run();
