<?php

use Tests\TestCase;
use ColetaDados\Scrappers\Departamentos;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;


class mockPDO extends \PDO
{
    public function __construct ()
    {}
}

class DepartamentosTest extends TestCase
{
    public function testGetDepartamentosReturnListOfDepartamentos() {
        $fetchAllMock = $this
            ->getMockBuilder('PDOStatement')
            ->setMethods(['fetchAll'])
            ->getMock();
        $fetchAllMock
            ->expects($this->any())
            ->method('fetchAll')
            ->will($this->returnValue([]));

        $mockPdo = $this->getMockBuilder(mockPDO::class)
            ->getMock();
        $mockPdo->method('prepare')
           ->will($this->returnValue($fetchAllMock));
        $path = __DIR__.'/Fixtures/departamentos/';
        $mock = $this->getMockBuilder(Departamentos::class)
            ->setConstructorArgs(['http://teste', $mockPdo])
            ->setMethods(['insert', 'update'])
            ->getMock();
        $mock->client = new HttpBrowser(new MockHttpClient([
            new MockResponse(file_get_contents($path . 'sitemap.xml')),
            new MockResponse(file_get_contents($path . 'departamento.xml')), //processSitemap
            new MockResponse(file_get_contents($path . 'produto-extra-fields.html')), //getProdutoFromMobile
            new MockResponse(file_get_contents(__DIR__ . '/Fixtures/Web/AddCarrinho.html')),
            new MockResponse(file_get_contents(__DIR__ . '/Fixtures/Web/AddCarrinho.html')),
            new MockResponse(file_get_contents(__DIR__ . '/Fixtures/Web/Stars-2Products-1Page.json')),
        ]));
        $response = $mock->processSitemapIndex();
        $this->assertJsonStringEqualsJsonFile($path . 'response.json', json_encode($response));
    }
}