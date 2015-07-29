<?php
namespace TypeRocket;

class MetaBox extends Registrable
{

    private $label = null;
    private $callback = null;
    private $context = null;
    private $priority = null;
    private $screens = array();
    private $args = array();

    /**
     * Make Meta Box
     *
     * @param null $name
     * @param null $screen
     * @param array $settings
     */
    public function __construct( $name, $screen = null, array $settings = array() )
    {
        $this->label = $this->id = $name;
        $this->id    = Sanitize::underscore( $this->id );

        if( ! empty( $screen ) ) {
            $screen = (array) $screen;
            $this->screens = array_merge($this->screens, $screen);
        }

        if (empty( $settings['callback'] )) {
            $settings['callback'] = array( $this, 'metaContent' );
        }
        if (empty( $settings['label'] )) {
            $settings['label'] = $this->label;
        } else {
            $this->label = $settings['label'];
        }

        unset( $settings['label'] );

        $defaults = array(
            'context'  => 'normal', // 'normal', 'advanced', or 'side'
            'priority' => 'high', // 'high', 'core', 'default' or 'low'
            'args'     => array()
        ); // arguments to pass into your callback function.

        $settings = array_merge( $defaults, $settings );

        $this->context = $settings['context'];
        $this->callback = $settings['callback'];
        $this->priority = $settings['priority'];
        $this->args = $settings['args'];
    }

    /**
     * Set the meta box label
     *
     * @param $label
     *
     * @return $this
     */
    public function setLabel($label) {

        $this->label = (string) $label;

        return $this;
    }

    /**
     * Set the meta box label
     *
     * @return $this->label
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Add meta box to a screen
     *
     * @param string|array $screen
     *
     * @return $this
     */
    public function addScreen($screen) {
        $this->screens = array_merge($this->screens, (array) $screen);

        return $this;
    }

    /**
     * Add content inside form hook and wrap with the TypeRocket container
     *
     * @param $object
     * @param $box
     */
    public function metaContent( $object, $box )
    {
        $func = 'add_meta_content_' . $this->id;

        echo '<div class="typerocket-container">';
        if (function_exists( $func )) :
            $func( $object, $box );
        elseif (TR_DEBUG == true) :
            echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
        endif;
        echo '</div>';
    }

    /**
     * Add meta box to post type
     *
     * @param string|PostType $s
     */
    public function postTypeRegistrationById( $s )
    {
        if ( ! is_string( $s )) {
            $s = (string) $s->getId();
        }

        if ( ! in_array( $s, $this->screens )) {
            $this->screens[] = $s;
        }

    }

    /**
     * Apply taxonomy to a post type by string
     *
     * @param $postTypeId
     */
    public function stringRegistration( $postTypeId )
    {
        $this->postTypeRegistrationById( $postTypeId );
    }

    /**
     * Register meta box with WordPress
     *
     * @return $this
     */
    public function register()
    {

        global $post, $comment;
        $type = get_post_type( $post->ID );
        if (post_type_supports( $type, $this->id )) {
            $this->postTypeRegistrationById( $type );
        }

        foreach ($this->screens as $v) {
            if ($type == $v || ( $v == 'comment' && isset( $comment ) ) || ( $v == 'dashboard' && !isset( $post ) ) ) {
                add_meta_box(
                    $this->id,
                    $this->label,
                    $this->callback,
                    $v,
                    $this->context,
                    $this->priority,
                    $this->args
                );
            }
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param null $priority
     *
     * @return MetaBox
     */
    public function setPriority( $priority )
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @param null $context
     *
     * @return MetaBox
     */
    public function setContext( $context )
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return null
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setCallback($callback) {

        if(is_callable($callback)) {
            $this->callback = $callback;
        } else {
            $this->callback = null;
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param array $args
     *
     * @return MetaBox
     */
    public function setArguments( array $args )
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->args;
    }

}