<?php

namespace PoK\Domain;

use PoK\ValueObject\Collection;

class Repository
{
    protected $gateway;
    protected $factory;
    protected $mapping;
    
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
        return $this;
    }
    
    public function setFactory($factory)
    {
        $this->factory = $factory;
        return $this;
    }
    
    public function setMapping($map)
    {
        $this->mapping = $map;
        return $this;
    }

    public function insert($instance)
    {
        return $this->gateway->insert($this->mapping->map($instance));
    }

    public function insertOrUpdate($instance)
    {
        return $this->gateway->insertOrUpdate($this->mapping->map($instance));
    }

    public function retrieveOne(...$parameters)
    {
        $data = $this->gateway->retrieveOne(...$parameters);

        return $data
            ? $this->factory->reconstitute(new Collection($data))
            : null;
    }

    // Make another one for paginated collection if necessary
    public function retrieveMultiple(...$parameters)
    {
        $data = $this->gateway->retrieveMultiple(...$parameters);

        if ($data instanceof Collection) {
            return $data
                ->map(function ($item) {
                    return $this->factory->reconstitute(new Collection($item));
                });
        } else {
            return new Collection([]);
        }
    }

    public function delete(...$conditionEqualCollection)
    {
        return $this->gateway->delete($this->mapping->map(...$conditionEqualCollection));
    }
}
