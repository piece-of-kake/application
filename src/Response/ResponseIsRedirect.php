<?php

namespace PoK\Response;

interface ResponseIsRedirect {
    public function getLocation(): string;
}
