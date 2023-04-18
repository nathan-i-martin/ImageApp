<?php

enum DataInstance {
    /**
     * The data is loaded from the database.
     * This should be used if the in-memory data can only be changed when the database is changed.
     */
    case Permanent;

    /**
     * The data only exists in-memory and does not exist in the database.
     */
    case Virtual;

    /**
     * The data exists in both the database and also in-memory.
     * The in-memory version has not been changes since it was pulled from the database.
     */
    case Synced;

    /**
     * The data exists in both the database and also in-memory.
     * However, the in-memory version has been changed and is not the same as the database version.
     */
    case Unsynced;
}