<?php

return [
    'company' => '',
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'projects' => [],
    'ip' => '',
    'stageDomain' => 'stage.com',
    'stageSubdomainPrefixes' => [
        'main_project' => [
            'WEB' => '',
            'Admin' => '-admin',
            'Callback' => '-cb',
            'RabbitMQ' => '-rmq',
        ],
        'service_project' => [
            'Admin' => '',
            'RabbitMQ' => '-rmq',
        ],
    ],
    'rsaKeysPath' => '',
    'commands' => [
        'create' => 'screen -dmS {ENV_ID} bash -c "~/stage/multistage.sh create {ENV_ID} {PROJECT} {SSH_USER} \'BACK_BRANCH\' \'FRONT_BRANCH\' > ~/logs/{DATE}-create-{ENV_ID}.log"',
        'update' => 'screen -dmS {ENV_ID} bash -c "~/stage/multistage.sh update {ENV_ID} {HASH_NAME} \'BACK_BRANCH\' \'FRONT_BRANCH\' > ~/logs/{DATE}-update-{ENV_ID}.log"',
        'delete' => 'screen -dmS {ENV_ID} bash -c "~/stage/multistage.sh delete {ENV_ID} {HASH_NAME} > ~/logs/{DATE}-delete-{ENV_ID}.log"',
        'updatebranch' => 'screen -dmS {ENV_ID} bash -c "~/stage/multistage.sh updatebranch {ENV_ID} {HASH_NAME} {ONE_BRANCH_DATA} > ~/logs/{DATE}-updatebranch-{ENV_ID}.log"',
    ],
    'connectionString' => 'ssh -J gateway@host:port user@CODE',
    'isMaintenanceMode' => false,
    'pathToLogs' => '',
    'pathToStoreConfigs' => '',
];
