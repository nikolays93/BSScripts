<?php

namespace Nikolays93\BSScripts;

class Accordion extends Collapse
{
    function set_defaults( $defaults )
    {
        $_def = array(
            'type' => 'a',
            'active' => 'active show',

            'data-parent' => '#accordion',
        );

        $this->def = apply_filters( 'Accordion::set_defaults', array_merge($this->def, $_def, $defaults) );
    }

    function pane( $collapse, $pane = '' )
    {
        ?>
        <div<?= $this->paneAttrs( $collapse ) ?>>
            <div class="card-body">
            <?= $pane ?>
            </div>
        </div>
        <?
    }

    public function render()
    {
        $controls = $this->__get('controls');
        $panes = $this->__get('panes');

        $parent = ( function_exists('mb_substr') ) ?
            mb_substr($this->def['data-parent'], 1) : substr($this->def['data-parent'], 1);

        if( $controls && $panes ) {
            foreach ($controls as $i => $control) {
                ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb0 mb-0">
                        <?php
                            echo $this->control($control);
                        ?>
                        </h5>
                    </div>
                    <?
                    echo $this->pane($panes[ $i ], $panes[ $i ]['pane']);
                    ?>
                </div><!-- .card -->
                <?php
            }
        }
    }
}