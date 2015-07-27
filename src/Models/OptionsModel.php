<?php
namespace TypeRocket\Models;

class OptionsModel extends Model
{

    function create( array $fields )
    {
        $this->saveOptions( $fields );

        return $this;
    }

    function update( $itemId, array $fields )
    {
        $this->saveOptions( $fields );

        return $this;
    }

    private function saveOptions( array $fields )
    {
        foreach ($fields as $key => $value) :

            if (is_string( $value )) {
                $value = trim( $value );
            }

            $current_meta = get_option( $key );

            if (( isset( $value ) && $value !== "" ) && $current_meta !== $value) :
                update_option( $key, $value );
            elseif ( ! isset( $value ) || $value === "" && ( isset( $current_meta ) || $current_meta === "" )) :
                delete_option( $key );
            endif;

        endforeach;
    }
}