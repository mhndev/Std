<?php
namespace Poirot\Core
{
    trait SetterSetup
    {
        /**
         * @var array List Setters By Priority
         * [
         *  'service_config',
         *  'listeners',
         *  // ...
         * ]
         *
         * application calls setter methods from top ...
         *
         */
        # protected $__setup_array_priority = array();

        /**
         * Setter Setup From Array
         *
         * @param array $setters        Associated Array
         *
         * @param bool  $throwException Throw Exception
         *
         * @throws \Exception
         * @return $this
         */
        function setupFromArray(array $setters, $throwException = false)
        {
            if (array_values($setters) == $setters)
                throw new \InvalidArgumentException('Setters Array must be associative array.');

            $sortQuee = $this->__setup_array_priority;
            uksort($setters, function($a, $b) use ($sortQuee) {
                // sort array to reach setter priorities
                $ai = array_search($a, $sortQuee);
                $ai = ($ai !== false) ? $ai : 1000;

                $bi = array_search($b, $sortQuee);
                $bi = ($bi !== false) ? $bi : 1000;

                return $ai < $bi ? -1 : ($ai == $bi) ? 0 : 1;
            });

            foreach($setters as $key => $val) {
                $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
                if (method_exists($this, $setter)) {
                    $this->{$setter}($val);
                } elseif($throwException) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'The option "%s" does not have a matching "%s" setter method',
                            $key, $setter
                        )
                    );
                }
            }

            return $this;
        }
    }

    class Entity
    {
        const DEFAULT_NONE_VALUE = null;

        // to detect get() default if not set
        const __not_set_value__ = '__not_set_value__';

        /**
         * Entity's items
         *
         * @var array
         */
        protected $properties = array();

        /**
         * Construct
         *
         * @param array|Entity $props Properties
         *
         * @throws \Exception
         */
        public function __construct($props = null)
        {
            if ($props) {
                if ($props instanceof Entity)
                    $props = $props->getAs(new self());

                if (!is_array($props))
                    throw new \Exception(
                        sprintf(
                            'Properties must instance of "Entity" or "Array" but "%s" given.',
                            is_object($props) ? get_class($props) : gettype($props)
                        )
                    );

                if (!empty($this->properties)) {
                    // maybe we have some predefined props field in class
                    // protected properties = array( .... );
                    $props = array_merge($this->properties, $props);
                }

                foreach($props as $key => $val) {
                    $this->set($key, $val);
                }
            }

            $this->consIt();
        }

        /**
         * Init Entity after construct
         */
        protected function consIt()
        {

        }

        /**
         * Get Property
         * - throw exception if property not found and default get not set
         *
         * @param string     $prop    Property name
         * @param null|mixed $default Default Value if not exists
         *
         * @throws \Exception
         * @return mixed
         */
        public function get($prop, $default = self::__not_set_value__)
        {
            if (!$this->has($prop) && $default === self::__not_set_value__)
                throw new \Exception(
                    sprintf('Property "%s" not found in entity.', $prop)
                );

            return ($this->has($prop))
                ? $this->properties[$prop]
                : $default;
        }

        /**
         * Set Property with value
         *
         * @param string $prop  Property
         * @param mixed  $value Value
         *
         * @return $this
         */
        public function set($prop, $value = self::DEFAULT_NONE_VALUE)
        {
            $this->properties[$prop] = $value;

            return $this;
        }

        /**
         * Set Properties
         *
         * @param Entity $entity
         *
         * @return $this
         */
        public function setFrom(Entity $entity)
        {
            $this->merge($entity);

            return $this;
        }

        /**
         * Merge/Set Data With Entity
         *
         * @param Entity $entity Merge Entity
         *
         * @return $this
         */
        public function merge(Entity $entity)
        {
            foreach($entity->keys() as $key)
                $this->set($key, $entity->get($key));

            return $this;
        }

        /**
         * Has Property
         *
         * @param string $prop Property
         *
         * @return boolean
         */
        public function has($prop)
        {
            return array_key_exists($prop, $this->properties);
        }

        /**
         * Delete a property
         *
         * @param string $prop Property
         *
         * @return $this
         */
        public function del($prop)
        {
            if (!$this->has($prop))
                unset($this->properties[$prop]);

            return $this;
        }

        /**
         * Get All Properties Keys
         *
         * @return array
         */
        public function keys()
        {
            return array_keys($this->properties);
        }

        /**
         * Get a copy of properties as hydrate structure
         *
         * @param Entity $entity Entity
         *
         * @return mixed
         */
        public function getAs(Entity $entity)
        {
            return $entity->setFrom($this)
                ->borrow();
        }

        /**
         * Output Conveyor Props. as desired manipulated data struct.
         *
         * @return mixed
         */
        public function borrow()
        {
            return $this->properties;
        }
    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays, the value from the second array
     * will be appended the the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the
     * one of the first array.
     *
     * @param  array $a
     * @param  array $b
     * @return array
     */
    function array_merge(array $a, array $b)
    {
        foreach ($b as $key => $value)
            if (array_key_exists($key, $a)) {
                if (is_int($key)) {
                    if (!in_array($value, $a))
                        $a[] = $value;
                }
                elseif (is_array($value) && is_array($a[$key]))
                    $a[$key] = array_merge($a[$key], $value);
                else
                    $a[$key] = $value;
            } else
                $a[$key] = $value;

        return $a;
    }
}
