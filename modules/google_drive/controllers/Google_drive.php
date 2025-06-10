<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Load the Google API PHP Client Library.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define a path to the credentials file.
define('CREDENTIALS_PATH', dirname(__DIR__) . '/config/credentials.json');
define('TOKEN_PATH', dirname(__DIR__) . '/config/token.json');

class Google_drive extends AdminController
{
    private $client;
    private $service;
    private $docsService;
    private $sheetsService;
    public function __construct()
    {
        parent::__construct();

        if (staff_cant('setting', 'google_drive') && staff_cant('view', 'google_drive') && staff_cant('create', 'google_drive') && staff_cant('edit', 'google_drive') && staff_cant('delete', 'google_drive')) {
            access_denied('google_drive');
        }

        $this->load->model('google_drive_model');
    }

    public function _create_client($type = '')
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName('Google Drive - ' . get_option('companyname'));
        if ($type == 'readonly') {
            $this->client->setScopes([
                Google_Service_Docs::DOCUMENTS_READONLY,
                Google_Service_Sheets::SPREADSHEETS_READONLY,
                Google_Service_Drive::DRIVE_READONLY,
                Google_Service_Drive::DRIVE_METADATA_READONLY,
                'email',
                'profile',
            ]);
        } else {
            $this->client->setScopes([
                Google_Service_Docs::DOCUMENTS,
                Google_Service_Sheets::SPREADSHEETS,
                Google_Service_Drive::DRIVE,
                Google_Service_Drive::DRIVE_FILE,
                Google_Service_Drive::DRIVE_METADATA,
                'email',
                'profile',
            ]);
        }
        $this->client->setAccessType('offline'); 
        $this->client->setAuthConfig(CREDENTIALS_PATH);
    }

    public function _set_service()
    {
        if (file_exists(TOKEN_PATH)) {
            $accessToken = json_decode(file_get_contents(TOKEN_PATH), true);
            $_SESSION['access_token'] = $accessToken;
            $this->client->setAccessToken($accessToken);
        }

        if (!isset($_SESSION['access_token'])) {
            $auth_url = $this->client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $_SESSION['access_token'] = $this->client->getAccessToken();
            } else {
                redirect($this->client->createAuthUrl());
                die;
            }
        }

        // Get a new instance of the Google Drive service
        $this->service = new Google_Service_Drive($this->client);
        $this->docsService = new Google_Service_Docs($this->client);
        $this->sheetsService = new Google_Service_Sheets($this->client);
    }

    public function docs()
    {
        if (staff_cant('setting', 'google_drive') && staff_cant('view', 'google_drive') && staff_cant('create', 'google_drive') && staff_cant('edit', 'google_drive') && staff_cant('delete', 'google_drive')) {
            access_denied('google_drive');
        }
		
		modules\google_drive\core\Apiinit::the_da_vinci_code(GOOGLE_DRIVE_MODULE);
		modules\google_drive\core\Apiinit::ease_of_mind(GOOGLE_DRIVE_MODULE);

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_drive', 'tables/docs'));
        }

        $data['title'] = _l('google_drive');

        $this->load->view('docs', $data);
    }

    public function sheets()
    {
        if (staff_cant('setting', 'google_drive') && staff_cant('view', 'google_drive') && staff_cant('create', 'google_drive') && staff_cant('edit', 'google_drive') && staff_cant('delete', 'google_drive')) {
            access_denied('google_drive');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('google_drive', 'tables/sheets'));
        }

        $data['title'] = _l('google_drive');

        $this->load->view('sheets', $data);
    }

    public function save()
    {
        $this->_create_client();
        
        $id = $this->input->post('id');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $type = $this->input->post('type');

        if ($title) {
            $this->_set_service();

            if ($type == 'doc') {
                if ($id) {
                    $google_doc = $this->google_drive_model->get($id);
    
                    if ($google_doc) {
                        try {
                            // Update the doc properties
                            $fileMetadata = new Google_Service_Drive_DriveFile([
                                'name' => $title
                            ]);
                            
                            $this->service->files->update($google_doc->driveid, $fileMetadata);                        
    
                            $requests = [
                                new Google_Service_Docs_Request([
                                    'insertText' => [
                                        'location' => [
                                            'index' => 1, // Insert at the beginning of the document
                                        ],
                                        'text' => $description,
                                    ]
                                ])
                            ];
                            // Execute the batchUpdate request
                            $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                                'requests' => $requests
                            ]);
                            $this->docsService->documents->batchUpdate($google_doc->driveid, $batchUpdateRequest);
        
                            $this->google_drive_model->update([
                                'title' => $title,
                                'description' => $description
                            ], $google_doc->id);
                            
                            echo json_encode([
                                'success' => true,
                                'id' => $id,
                                'message' => _l('google_drive_doc_saved_successfully', _l('google_drive')),
                            ]);
                        } catch (Exception $e) {
                            echo json_encode([
                                'success' => false,
                                'message' => _l('google_drive_integrate_again', _l('google_drive')),
                                'redirect_url' => admin_url('google_drive/settings')
                            ]);
                        }
                    }
                } else {
                    try {
                        // Create a new document
                        $fileMetadata = new Google_Service_Drive_DriveFile([
                            'name' => $title,
                            'mimeType' => 'application/vnd.google-apps.document'
                        ]);
                        
                        $document = $this->service->files->create($fileMetadata, ['fields' => 'id']);
                        
                        // Get the document ID
                        $documentId = $document->id;
    
                        $requests = [
                            new Google_Service_Docs_Request([
                                'insertText' => [
                                    'location' => [
                                        'index' => 1, // Insert at the beginning of the document
                                    ],
                                    'text' => $description,
                                ]
                            ])
                        ];
                        // Execute the batchUpdate request
                        $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                            'requests' => $requests
                        ]);
                        $this->docsService->documents->batchUpdate($documentId, $batchUpdateRequest);
        
                        $new_doc_id = $this->google_drive_model->add([
                            'staffid' => get_staff_user_id(),
                            'driveid' => $documentId,
                            'title' => $title,
                            'type' => 'doc',
                            'description' => $description
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'id' => $new_doc_id,
                            'message' => _l('google_drive_doc_created_successfully', _l('google_drive')),
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => _l('google_drive_integrate_again', _l('google_drive')),
                            'redirect_url' => admin_url('google_drive/settings')
                        ]);
                    }
                }
            }
            if ($type == 'sheet') {
                if ($id) {
                    $google_sheet = $this->google_drive_model->get($id);

                    if ($google_sheet) {
                        try {
                            // Update the spreadsheet properties
                            $requests = [
                                new Google_Service_Sheets_Request([
                                    'updateSpreadsheetProperties' => [
                                        'properties' => [
                                            'title' => $title,
                                        ],
                                        'fields' => 'title',
                                    ],
                                ]),
                                new Google_Service_Sheets_Request([
                                    'createDeveloperMetadata' => [
                                        'developerMetadata' => [
                                            'metadataKey' => 'description',
                                            'metadataValue' => $description,
                                            'location' => [
                                                'spreadsheet' => true,
                                            ],
                                            'visibility' => 'DOCUMENT',
                                        ],
                                    ],
                                ]),
                            ];
    
                            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                                'requests' => $requests,
                            ]);
    
                            $response = $this->sheetsService->spreadsheets->batchUpdate($google_sheet->driveid, $batchUpdateRequest);
    
                            $this->google_drive_model->update([
                                'title' => $title,
                                'description' => $description
                            ], $google_sheet->id);
                            
                            echo json_encode([
                                'success' => true,
                                'id' => $id,
                                'message' => _l('google_drive_sheet_created_successfully', _l('google_drive')),
                            ]);
                        } catch (Exception $e) {
                            echo json_encode([
                                'success' => false,
                                'message' => _l('google_drive_integrate_again', _l('google_drive')),
                                'redirect_url' => admin_url('google_drive/settings')
                            ]);
                        }
                    }
                } else {
                    try {
                        // Create a new spreadsheet
                        $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                            'properties' => [
                                'title' => $title
                            ]
                        ]);
                
                        // Execute the request
                        $spreadsheet = $this->sheetsService->spreadsheets->create($spreadsheet, [
                            'fields' => 'spreadsheetId'
                        ]);
                        // Get the spreadsheet ID
                        $spreadsheetId = $spreadsheet->spreadsheetId;
    
                        if ($description) {
                            // Add a new sheet to the spreadsheet
                            $requests = [
                                new Google_Service_Sheets_Request([
                                    'createDeveloperMetadata' => [
                                        'developerMetadata' => [
                                            'metadataKey' => 'description',
                                            'metadataValue' => $description,
                                            'location' => [
                                                'spreadsheet' => true
                                            ],
                                            'visibility' => 'DOCUMENT'
                                        ]
                                    ]
                                ])
                            ];
                            $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                                'requests' => $requests
                            ]);
    
                            $this->sheetsService->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
                        }
    
                        $sheet_id = $this->google_drive_model->add([
                            'staffid' => get_staff_user_id(),
                            'driveid' => $spreadsheetId,
                            'title' => $title,
                            'type' => 'sheet',
                            'description' => $description
                        ]);
                        
                        echo json_encode([
                            'success' => true,
                            'id' => $sheet_id,
                            'message' => _l('google_drive_sheet_created_successfully', _l('google_drive')),
                        ]);
                    } catch (Exception $e) {
                        echo json_encode([
                            'success' => false,
                            'message' => _l('google_drive_integrate_again', _l('google_drive')),
                            'redirect_url' => admin_url('google_drive/settings')
                        ]);
                    }
                }
            }
        }
    }

    public function view($id)
    {
        if (staff_cant('view', 'google_drive')) {
            access_denied('google_drive');
        }

        $this->_create_client();
        $this->_set_service();
        
        $google_drive = $this->google_drive_model->get($id);

        if ($google_drive) {
            $data['id']                     = $id;
            $data['title']                  = _l('google_drive') . ' - ' . $google_drive->title;
            $data['driveid']                = $google_drive->driveid;
            $data['type']                   = $google_drive->type;
            $this->load->view('view', $data);
        } else {
            redirect(admin_url('google_drive/docs'));
        }
    }

    public function delete($id)
    {
        if (staff_cant('delete', 'google_drive')) {
            access_denied('google_drive');
        }

        $this->_create_client();
        $this->_set_service();

        $google_drive = $this->google_drive_model->get($id);
        $drive_type = $google_drive->type;
        $drive = new Google_Service_Drive($this->client);
        try {
            $drive->files->delete($google_drive->driveid);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        $this->google_drive_model->delete($id);

        set_alert('success', _l('google_drive_' . $drive_type . '_deleted_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/' . $drive_type . 's'));
    }

    public function update_doc($docId, $contents)
    {
        if (staff_cant('view', 'google_drive') || staff_cant('create', 'google_drive') || staff_cant('edit', 'google_drive')) {
            access_denied('google_drive');
        }

        $this->_create_client();
        
        try {
            // Prepare the requests to update the document content
            $requests = [
                new Google_Service_Docs_Request([
                    'insertText' => [
                        'location' => [
                            'index' => 1,
                        ],
                        'text' => $contents
                    ],
                ])
            ];

            // Execute the batch update
            $batchUpdateRequest = new Google_Service_Docs_BatchUpdateDocumentRequest([
                'requests' => $requests
            ]);
            $result = $this->service->documents->batchUpdate($docId, $batchUpdateRequest);
        } catch (Exception $e) {
            echo 'Error writing to document: ', $e->getMessage(), "\n";
            return null;
        }
        
        set_alert('success', _l('google_drive_doc_updated_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/docs'));
    }

    public function update_sheet($sheetId, $range, $values)
    {
        if (staff_cant('view', 'google_drive') || staff_cant('create', 'google_drive') || staff_cant('edit', 'google_drive')) {
            access_denied('google_drive');
        }

        $this->_create_client();
        
        try {
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
            $params = [
                'valueInputOption' => 'RAW'
            ];
            $result = $this->sheetsService->spreadsheets_values->update($sheetId, $range, $body, $params);
            return $result->getUpdatedCells();
        } catch (Exception $e) {
            echo 'Error writing to sheet: ', $e->getMessage(), "\n";
            return null;
        }
        
        set_alert('success', _l('google_drive_sheet_updated_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/sheets'));
    }

    public function integrate()
    {
        if (staff_cant('setting', 'google_drive')) {
            access_denied('google_drive');
        }

        foreach(['client_id', 'client_secret'] as $option) {
            $$option = $this->input->post($option);
            $$option = trim($$option);
            $$option = nl2br($$option);
        }
        
        if (file_exists(CREDENTIALS_PATH)) {
            $credentials = json_decode(file_get_contents(CREDENTIALS_PATH), true);
        } else {
            $credentials = [
                'web' => [
                    "client_id" => "",
                    "project_id" => "themesic-linkforsa",
                    "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
                    "token_uri" => "https://oauth2.googleapis.com/token",
                    "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
                    "client_secret" => "",
                    "redirect_uris" => [admin_url('google_drive/redirects')]
                ]
            ];
        }
        $credentials['web']['client_id'] = $client_id;
        $credentials['web']['client_secret'] = $client_secret;
        file_put_contents(CREDENTIALS_PATH, json_encode($credentials));

        update_option('google_drive_client_id', $client_id);
        update_option('google_drive_client_secret', $client_secret);
        
        $this->_create_client();
        $authUrl = $this->client->createAuthUrl();
        
        echo json_encode([
            'status' => 'success',
            'auth_url' => $authUrl,
        ]);
    }

    public function redirects()
    {
        $code = $this->input->get('code');
        
        if ($code) {
            $this->_create_client();
    
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);
    
            // Save the token to a file.
            if (!file_exists(dirname(TOKEN_PATH))) {
                mkdir(dirname(TOKEN_PATH), 0700, true);
            }
            file_put_contents(TOKEN_PATH, json_encode($this->client->getAccessToken()));
            
            set_alert('success', _l('google_drive_integration_successfully', _l('google_drive')));
        }
        
        redirect(admin_url('google_drive/settings?tab=tab_integration'));
    }

    public function settings()
    {
        if (staff_cant('setting', 'google_drive')) {
            access_denied('google_drive');
        }

        $data['title'] = _l('google_drive_settings');
        $this->load->view('settings', $data);
    }

    public function fetch_doc()
    {
        $this->_create_client('readonly');
        $this->_set_service();

        $drive = new Google_Service_Drive($this->client);

        $files = [];

        try {
            $response = $drive->files->listFiles([
                'q' => "mimeType='application/vnd.google-apps.document' and trashed=false",
                'fields' => 'files(id, name)',
            ]);

            foreach ($response->getFiles() as $file) {
                $files[] = $file;
            }
        } catch (Exception $e) {
            set_alert('success', _l('google_drive_integrate_again', _l('google_drive')));
            redirect(admin_url('google_drive/settings'));
        }
        
        $google_doc_ids = [];
        foreach ($files as $file) {
            $google_doc_ids[] = $file->getId();
            $google_doc = $this->google_drive_model->get_by_driveid($file->getId());
            if ($google_doc) {
                $this->google_drive_model->update([
                    'title' => $file->getName()
                ], $google_doc->id);
            } else {
                $this->google_drive_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $file->getId(),
                    'title' => $file->getName(),
                    'type' => 'doc',
                    'description' => ''
                ]);
            }
        }

        $google_docs = $this->google_drive_model->get_all('doc');
        foreach ($google_docs as $google_doc) {
            if (!in_array($google_doc['driveid'], $google_doc_ids)) {
                $this->google_drive_model->delete($google_doc['id']);
            }
        }
        
        set_alert('success', _l('google_drive_doc_fetched_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/docs'));
    }

    public function fetch_sheet()
    {
        $this->_create_client('readonly');
        $this->_set_service();

        $drive = new Google_Service_Drive($this->client);

        $files = [];

        try {
            $response = $drive->files->listFiles([
                'q' => "mimeType='application/vnd.google-apps.spreadsheet' and trashed=false",
                'fields' => 'files(id, name)',
            ]);

            foreach ($response->getFiles() as $file) {
                $files[] = $file;
            }
        } catch (Exception $e) {
            set_alert('success', _l('google_sheet_integrate_again', _l('google_sheet')));
            redirect(admin_url('google_sheet/settings'));
        }
        
        $google_sheet_ids = [];
        foreach ($files as $file) {
            $google_sheet_ids[] = $file->getId();
            $google_sheet = $this->google_drive_model->get_by_driveid($file->getId());
            if ($google_sheet) {
                $this->google_drive_model->update([
                    'title' => $file->getName()
                ], $google_sheet->id);
            } else {
                $this->google_drive_model->add([
                    'staffid' => get_staff_user_id(),
                    'driveid' => $file->getId(),
                    'title' => $file->getName(),
                    'type' => 'sheet',
                    'description' => ''
                ]);
            }
        }

        $google_sheets = $this->google_drive_model->get_all('sheet');
        foreach ($google_sheets as $google_sheet) {
            if (!in_array($google_sheet['driveid'], $google_sheet_ids)) {
                $this->google_drive_model->delete($google_sheet['id']);
            }
        }
        
        set_alert('success', _l('google_drive_sheet_fetched_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/sheets'));
    }

    public function reset_settings()
    {
        if (staff_cant('setting', 'google_drive')) {
            access_denied('google_drive');
        }

        update_option('google_drive_client_id', '');
        update_option('google_drive_client_secret', '');

        unlink(CREDENTIALS_PATH);
        unlink(TOKEN_PATH);
        
        set_alert('success', _l('google_drive_reseted_successfully', _l('google_drive')));

        redirect(admin_url('google_drive/settings'));
    }

    public function save_settings()
    {
        if (staff_cant('setting', 'google_drive')) {
            access_denied('google_drive');
        }

        hooks()->do_action('before_save_google_doc');

        foreach(['can_access', 'can_manage'] as $option) {
            // Also created the variables
            $$option = $this->input->post($option);
            $$option = trim($$option);
            $$option = nl2br($$option);
        }
    }
}