<?php

namespace app\components;

class Constants {

    const USER_TYPE_CONSOLE = -2;
    const USER_TYPE_SADMIN = -1;
    const USER_TYPE_MSO = 0;
    const USER_TYPE_DISTRIBUTOR = 1;
    const USER_TYPE_OPERATOR = 2;
    const USER_TYPE_OPT_STAFF = 3;
    const USER_TYPE_MSO_USER = 4;
    const USER_TYPE_SUBSCRIBER = 5;
    const USER_TYPE_SADMIN_LABEL = "SADMIN";
    const USER_TYPE_MSO_LABEL = "MSO";
    const USER_TYPE_DISTRIBUTOR_LABEL = "DISTRIBUTOR";
    const USER_TYPE_OPERATOR_LABEL = "OPERATOR";
    const USER_TYPE_OPT_STAFF_LABEL = "STAFF";
    const USER_TYPE_MSO_USER_LABEL = "ADMIN";
    const USER_TYPE_SUBSCRIBER_LABEL = "SUBSCRIBER";
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_BLOCKED = -1;
    const LABEL_STATUS = [
        self::STATUS_ACTIVE => "Active",
        self::STATUS_INACTIVE => "In Active",
        self::STATUS_BLOCKED => "Blocked",
    ];
    const LABEL_USER_TYPE = [
        self::USER_TYPE_SADMIN => self::USER_TYPE_SADMIN_LABEL,
        self::USER_TYPE_MSO => self::USER_TYPE_MSO_LABEL,
        self::USER_TYPE_DISTRIBUTOR => self::USER_TYPE_DISTRIBUTOR_LABEL,
        self::USER_TYPE_OPERATOR => self::USER_TYPE_OPERATOR_LABEL,
        self::USER_TYPE_OPT_STAFF => self::USER_TYPE_OPT_STAFF_LABEL,
        self::USER_TYPE_MSO_USER => self::USER_TYPE_MSO_USER_LABEL,
        self::USER_TYPE_SUBSCRIBER => self::USER_TYPE_SUBSCRIBER_LABEL,
    ];
    const PFX_OPT_MSO = "RM";
    const PFX_OPT_DISTRIBUTOR = "RD";
    const PFX_OPT_OPERATOR = "RO";
    const PFX_AREA = "RA";
    const PFX_SOCIETY = "RS";

}
