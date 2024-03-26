<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use WSSC\Components\ClientConfig;
use WSSC\WebSocketClient;

class anotherClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'another-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connects to a WebSocket server to receive data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Your WebSocket server URL including API key
        $url = env('WEBSOCKET_URL', 'wss://s12218.nyc1.piesocket.com/v3/1?api_key=3hMVK8wsPgzHX9FxQ6VkJgw1g3mIt1UFhuwwhTJ7');

        while (true) {
            try {
                $config = new ClientConfig();
                $config->setContextOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]);

                $client = new WebSocketClient($url, $config);

                if ($client->isConnected() === true) {
                    $this->info('Connected to WebSocket server');
                    $this->info("Send text to WebSocket server using the tool 'https://piehost.com/websocket-tester'");
                    $this->info("Then, you will see the text here.");

                    // Send initial request
                    $client->send('Hello PieSocket!');

                    while (true) {
                        try {
                            $data = $client->receive();
                            $this->processData($data);
                        } catch (\Exception $e) {
                            //$this->log("Error: ".$e->getMessage());
                        }
                    }
                } else {
                    $this->error('Failed to connect');
                }
            } catch (\Exception $e) {
                $this->error("Connection failed: " . $e->getMessage());
                sleep(10); // Wait for 10 seconds before trying to reconnect
            }
        }
    }

    protected function processData($data)
    {
        // TODO for additional processing.
        $this->info($data);
    }
}
