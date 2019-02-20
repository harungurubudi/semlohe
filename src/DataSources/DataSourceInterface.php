<?php
namespace App\Semlohe\DataSources;

interface DataSourceInterface
{
    public function insert(array $data, array $actor = []);
    public function update(array $data, $id, array $actor = []);
    public function delete($id, array $actor = []);
    public function toggleStatus($id, $status, array $actor = []);
    public function getById($id, array $actor = []);
    public function getCollection(array $filters = [], array $sorting = [], $page = 1, array $actor = []);
    
}