<?php

namespace pjdietz\RestCms\Database\TempTable;

/**
 * Interface for classes for constructing temporary tables.
 */
interface TempTableInterface
{
    /** Drop the temporary table, if the instance created one. */
    public function drop();

    /**
     * @return bool A temporary table was created and should be joined to.
     */
    public function isRequired();
}
