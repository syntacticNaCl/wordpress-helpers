<?php
namespace Zawntech\WordPress\PostTypes;

class PostMeta
{
    /**
     * @var int The post ID.
     */
    protected $id;

    /**
     * @var mixed An array loaded on instantiation.
     */
    protected $meta;

    /**
     * @var array An array to type cast post meta.
     */
    protected $casts = [];

    /**
     * PostMeta constructor.
     * @param $postId int
     */
    public function __construct($postId)
    {
        $this->id = $postId;
        $this->meta = get_post_meta($postId);

        // Loop through this object's public properties.
        foreach( $this->getProperties() as $property )
        {
            $metaValue = isset($this->meta[$property]) ? $this->meta[$property][0] : false;
            $this->{$property} = $metaValue;
        }
    }

    protected function getProperties()
    {
        return array_diff( array_keys( get_object_vars($this) ), ['id', 'meta', 'casts'] );
    }

    public function save()
    {
        // Update properties.
        foreach( $this->getProperties() as $property )
        {
            update_post_meta( $this->id, $property, $this->{$property} );
        }
    }

    protected function cast($data, $type = 'string')
    {
        if ( 'string' === $type ) {
            return $data;
        }

        if ( 'array' === $type ) {
            return json_decode($data, true);
        }

        if ( 'object' === $type ) {
            return json_decode($data);
        }
    }
    
    public function __get($property)
    {
        if (isset($this->$property))
        {
            return $this->$property;
        } else {
            return false;
        }
    }

    public function __set($property, $value)
    {
        // If the property is not set, return false.
        if (! isset($this->$property) )
        {
            return false;
        }

        // Type cast data?
        if ( isset( $this->casts[$property] ) )
        {
            return $this->$property = $this->cast($value, $this->casts[$property]);
        }

        return $this->$property = $value;
    }
}