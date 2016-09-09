<?php
    include 'BasicEnum.php';

    abstract class InputType extends BasicEnum {
        const INPUT = array('text', 'textarea', 'date', 'list');
    }
