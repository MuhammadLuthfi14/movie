<?php

namespace App\Interfaces;

interface MovieRepositoryInterface
{
    public function getAllPaginated(int $perPage = 10, string $search = null);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAllCategories();
}