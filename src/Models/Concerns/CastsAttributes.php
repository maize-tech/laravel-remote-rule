<?php

namespace Maize\RemoteRule\Models\Concerns;

trait CastsAttributes
{
    public function initializeCastsAttributes()
    {
        $this->mergeCasts(
            config('remote-rule.attribute_cast')
        );
    }
}
