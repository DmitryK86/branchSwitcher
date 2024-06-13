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
        'addssh' => 'screen -dmS {ENV_ID} bash -c "~/stage/ssh.sh addssh {HASH_NAME} {SSH_USER} > ~/logs/{DATE}-addssh-{ENV_ID}.log"',
        'remove_auth' => 'sudo ~/stage/openaccess.sh open {HASH_NAME} {MINUTES} > ~/logs/{DATE}-openaccess-{ENV_ID}.log',
        'reload' => 'lxc stop -f {HASH_NAME} && lxc start {HASH_NAME}',
        'updateDB' => '~/stage/updatedb.sh {HASH_NAME} > ~/logs/{DATE}-updateDB-{ENV_ID}.log',
    ],
    'connectionString' => 'ssh -J gateway@host:port user@CODE',
    'isMaintenanceMode' => false,
    'pathToLogs' => '',
    'pathToStoreConfigs' => '',
    'autotest' => [
        'url' => '',
        'login' => '',
        'password' => '',
    ],
    'publicApiKey' => 'some-key',
    'foreignEnvs' => [
        'url' => 'https://kube01-ams5.ams.infng.net:8443/api/v1/namespaces',
        'limit' => 500,
        'token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6Im85LXBFTEVfTy1EYVlHdXE0NDdyR2NYWk92WGpiamRoRFRzb2k0N29UeDAifQ.eyJhdWQiOlsiaHR0cHM6Ly9rdWJlcm5ldGVzLmRlZmF1bHQuc3ZjLmNsdXN0ZXIubG9jYWwiXSwiZXhwIjoxNzQ4OTAzMjg2LCJpYXQiOjE3MTczNjcyODYsImlzcyI6Imh0dHBzOi8va3ViZXJuZXRlcy5kZWZhdWx0LnN2Yy5jbHVzdGVyLmxvY2FsIiwia3ViZXJuZXRlcy5pbyI6eyJuYW1lc3BhY2UiOiJrdWJlLXN5c3RlbSIsInNlcnZpY2VhY2NvdW50Ijp7Im5hbWUiOiJuYW1lc3BhY2Utdmlld2VyIiwidWlkIjoiOTc2OWQ1NjYtYzAyYi00NDAxLThmOTctYzc0M2NhZjU5NzlkIn19LCJuYmYiOjE3MTczNjcyODYsInN1YiI6InN5c3RlbTpzZXJ2aWNlYWNjb3VudDprdWJlLXN5c3RlbTpuYW1lc3BhY2Utdmlld2VyIn0.WtDW6hzCIfF4rw--AcQXSpnr_JSySAf-IsNYkJRcIq1bhm-op3WvdmPNG1wi0g3_t2zWcbVoQ-NPa4bp4B8ABruBWtMiTGs6xZIAppViCuhLcL8Z1f7T1b7EAVb6LD69-veyI3rXglOzR20VN7XxBxOvK_oUu0uD4BURgTo5hRsQA5c3B0yIt5E14IYSR55N9ehY7dnp1IObkIaHddzBzyAT7TyfZeYKk_52le_E6yxKea0sEoFECg7p_g4JFSpG4w4ZIhk7_j9-7wutmeSqlJ1Uz3As5XEUnca5zHCSr37CGYC994KkyHr98QdvqvcPGzaltD6OWIm1UNFyij7beA',
    ],
];
