<?php

$container->loadFromExtension('dots_united_doc_check', array(
    'login_id' => 123456789,
    'login_form' => array(
        'template'           => 'xl_red',
        'base_url'           => 'http://example.com',
        'append_session_id'  => true,
        'special_parameters' => 'special1=foo',
        'csrf_protection'    => false,
        'csrf_field_name'    => '_doccheck_token',
        'secret'             => '$3cr3t'
    )
));
