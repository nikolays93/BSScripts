<?php

namespace BSScripts;

class Tabs extends Collapse
{
    function set_defaults( $defaults )
    {
        $_def = array(
            'type' => 'a',
            'data-toggle' => 'tab',
            'active' => 'active show',
            'ctrlClass' => 'nav-link',
            'paneClass' => 'tab-pane',
        );

        $this->def = apply_filters( 'Tabs::set_defaults', array_merge($this->def, $_def, $defaults) );
    }

    function pane( $collapse, $pane = '' )
    {
        $attrs = $this->paneAttrs( $collapse );
        ?>
        <div<?= $attrs ?>>
            <?= $pane ?>
        </div>
        <?
    }

    public function render()
    {
        ?>
        <nav>
            <div class="nav nav-tabs" role="tablist">
            <?php
            if( $controls = parent::__get('controls') ) {
                foreach ($controls as $collapse) {
                    echo $this->control($collapse);
                }
            }
            ?>
        </nav>

        <div class="tab-content">
            <?php if( $panes = parent::__get('panes') ) : foreach ($panes as $collapse): ?>
                <?php $this->pane($collapse, $collapse['pane']); ?>
            <?php endforeach; endif; ?>
        </div>
        <?
    }
}