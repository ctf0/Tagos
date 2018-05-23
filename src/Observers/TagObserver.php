<?php

namespace ctf0\Tagos\Observers;

class TagObserver
{
    /**
     * Listen to the Tag saved event.
     */
    public function saved()
    {
        return $this->cleanData();
    }

    /**
     * Listen to the Tag deleted event.
     */
    public function deleted()
    {
        return $this->cleanData();
    }

    /**
     * helpers.
     *
     * @return [type] [description]
     */
    protected function cleanData()
    {
        return app('cache')->forget('tagos');
    }
}
