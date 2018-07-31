<?php

namespace Nikolays93\BSScripts;

if( !function_exists('apply_filters') ) {
    function apply_filters( $tag, $value = false, $arg1 = null, $arg2 = null, $arg3 = null ) {
        return $value;
    }
}

// add_filter( 'Collapse::ControlAttrs', 'sanitize_array', 10, 1 );
// function sanitize_array( $arr ) {
// }

class Collapse
{
    private $active;
    private $data = array();
    protected $def = array(
        'type' => 'button',
        'ctrlClass' => 'btn',
        'data-toggle' => 'collapse',
        'data-target' => '#%s',
        'role' => 'button',
        'active' => 'show',
        'paneClass' => 'collapse',
        'data-parent' => '',
    );

    static function example()
    {
        $_ex = 'Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.';

        $ex = apply_filters('PHPBootstrap::example', $_ex);

        return $ex;
    }

    function __add( $key, $val = array() )
    {
        if( empty($val['id']) && apply_filters('Collapse::check_element_id', 1) )
            return;

        if( !$this->active )
            $this->active = $val['id'];

        $this->data[ $key ][] = $val;
    }

    function __get( $key )
    {
        if( isset( $this->data[ $key ] ) )
            return $this->data[ $key ];

        return false;
    }

    function __construct($collapses = array(), $active = false, $defaults = array())
    {
        $this->set_defaults( $defaults );
        $this->active = $active;

        foreach ($collapses as $collapse) {
            if( empty($collapse['id']) )
                continue;

            $this->add( $collapse );
        }
    }

    function set_defaults( $defaults )
    {
        $this->def = apply_filters( 'Collapse::set_defaults', array_merge($this->def, $defaults) );

    }

    function controlAttrs( $collapse )
    {
        $_attrs = array(
            'class' => isset( $collapse['ctrlClass'] ) ?
                $collapse['ctrlClass'] : $this->def['ctrlClass'],

            'data-toggle' => $this->def['data-toggle'],

            'data-target' => isset( $collapse['data-target'] ) ?
                $collapse['data-target'] : sprintf($this->def['data-target'], $collapse['id']),

            'role' => $this->def['role'],

            'aria-expanded' => 'false',

            'aria-controls' => isset( $collapse['aria-controls'] ) ?
                $collapse['aria-controls'] : $collapse['id'],
        );

        if( $this->active == $collapse['id'] ) {
            $_attrs['class'] .= ' ' . $this->def['active'];
            $_attrs['aria-expanded'] = 'true';
        }

        if( 'button' == $collapse['type'] ) {
            $_attrs['type'] = 'button';
        }
        elseif( 'a' == $collapse['type'] ) {
            $_attrs['href'] = $_attrs['data-target'];
            unset( $_attrs['data-target'] );
        }

        $attrs = apply_filters('Collapse::controlAttrs', $_attrs, $collapse);
        $res = array();
        foreach ($attrs as $key => $value) {
            $res[] = sprintf('%s="%s"', $key, $value);
        }

        if( !count($res) )
            return '';

        return (' ' . implode(' ', $res));
    }

    function paneAttrs( $collapse )
    {
        $_attrs = array(
            'class' => $this->def['paneClass'],
            'id' => $collapse['id'],
        );

        if( $this->def['data-parent'] || !empty($collapse['data-parent']) ) {
            $_attrs['data-parent'] = !empty($collapse['data-parent']) ?
                $collapse['data-parent'] : $this->def['data-parent'];
        }

        if($this->active == $collapse['id'])
            $_attrs['class'] .= ' ' . $this->def['active'];

        if( isset($collapse['paneClass']) && $collapse['paneClass'] )
            $_attrs['class'] .= ' ' . $collapse['paneClass'];

        $attrs = apply_filters('Collapse::paneAttrs', $_attrs, $collapse);
        $res = array();
        foreach ($attrs as $key => $value) {
            $res[] = sprintf('%s="%s"', $key, $value);
        }

        if( !count($res) )
            return '';

        return (' ' . implode(' ', $res));
    }

    function control( $collapse )
    {
        printf('<%1$s%2$s>%3$s</%1$s>',
            $collapse['type'],
            $this->controlAttrs( $collapse ),
            $collapse['control']
        );
    }

    function pane( $collapse, $pane = '' )
    {
        $attrs = $this->paneAttrs( $collapse );
        ?>
        <div<?= $attrs ?>>
            <div class="card card-body">
                <?= $pane ?>
            </div>
        </div>
        <?
    }

    public function render()
    {
        ?>
        <p>
        <?php
        if( $controls = $this->__get('controls') ) {
            foreach ($controls as $collapse) {
                echo $this->control($collapse);
            }
        }
        ?>
        </p>

        <div class="row">
            <?php if( $panes = $this->__get('panes') ) : foreach ($panes as $collapse): ?>
                <div class="col">
                    <?php $this->pane($collapse, $collapse['pane']); ?>
                </div>
            <?php endforeach; endif; ?>
        </div>
        <?
    }

    public function add( $collapse ) {
        /**
         * Get control
         */
        if( empty($collapse['type']) )
            $collapse['type'] = $this->def['type'];

        /**
         * Get pane
         */
        if( isset($collapse['pane']) && is_array($collapse['pane']) ) {
            $collapse['pane'] = is_callable($collapse['pane']) ?
                call_user_func( $collapse['pane'] ) : '';
        }

        if( !empty( $collapse['control'] ) )
            $this->__add( 'controls', $collapse );

        if( !empty($collapse['pane']) )
            $this->__add( 'panes', $collapse );
    }
}
