<?php

namespace App\Services;

class FakultasService
{
    /**
     * Default faculty mapping data
     */
    protected array $fakultasData = [
        ['id_fakultas' => 1, 'fakul' => 'FITK', 'fakultas' => 'ILMU TARBIYAH DAN KEGURUAN'], 
        ['id_fakultas' => 2, 'fakul' => 'FEBI', 'fakultas' => 'EKONOMI DAN BISNIS ISLAM'], 
        ['id_fakultas' => 3, 'fakul' => 'FS', 'fakultas' => 'SYARIAH'], 
        ['id_fakultas' => 4, 'fakul' => 'FDKI', 'fakultas' => 'DAKWAH DAN KOMUNIKASI ISLAM'], 
        ['id_fakultas' => 5, 'fakul' => 'FUA', 'fakultas' => 'USHULUDDIN DAN ADAB'], 
        ['id_fakultas' => 6, 'fakul' =>  'S2', 'fakultas' => 'PASCASARJANA'],
        ['id_fakultas' => 7, 'fakul' =>  'S3', 'fakultas' => 'PASCASARJANA'],
    ];

    /**
     * Get all faculty data
     */
    public function getAllFakultas(): array
    {
        return $this->fakultasData;
    }

    /**
     * Find faculty data by faculty name or code
     * 
     * @param string $fakultas Faculty name or code to search for
     * @return array|null Returns array with id_fakultas and fakul, or null if not found
     */
    public function getFakulByFakultas(string $fakultas): ?array
    {
        $normalized = strtolower(trim($fakultas));

        foreach ($this->fakultasData as $item) {
            // Match full faculty name
            if (isset($item['fakultas']) && strtolower($item['fakultas']) === $normalized) {
                return $this->formatFacultyResult($item);
            }
            
            // Match faculty code
            if (isset($item['fakul']) && strtolower($item['fakul']) === $normalized) {
                return $this->formatFacultyResult($item);
            }
        }

        return null;
    }

    /**
     * Get faculty by ID
     */
    public function getFakulById(int $id): ?array
    {
        foreach ($this->fakultasData as $item) {
            if ($item['id_fakultas'] === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get faculty code by faculty name
     */
    public function getFacultyCodeByName(string $facultyName): ?string
    {
        $faculty = $this->getFakulByFakultas($facultyName);
        return $faculty['fakul'] ?? null;
    }

    /**
     * Get faculty name by code
     */
    public function getFacultyNameByCode(string $code): ?string
    {
        foreach ($this->fakultasData as $item) {
            if (strtolower($item['fakul']) === strtolower(trim($code))) {
                return $item['fakultas'];
            }
        }

        return null;
    }

    /**
     * Check if faculty exists
     */
    public function facultyExists(string $fakultas): bool
    {
        return $this->getFakulByFakultas($fakultas) !== null;
    }

    /**
     * Get list of all faculty codes
     */
    public function getAllFacultyCodes(): array
    {
        return array_column($this->fakultasData, 'fakul');
    }

    /**
     * Get list of all faculty names
     */
    public function getAllFacultyNames(): array
    {
        return array_column($this->fakultasData, 'fakultas');
    }

    /**
     * Format faculty result for consistent output
     */
    protected function formatFacultyResult(array $item): ?array
    {
        if (!isset($item['fakul'], $item['id_fakultas'])) {
            return null;
        }

        return [
            'id_fakultas' => $item['id_fakultas'],
            'fakul' => $item['fakul'],
            'fakultas' => $item['fakultas'] ?? null
        ];
    }

    /**
     * Search faculties by partial name match
     */
    public function searchFaculties(string $query): array
    {
        $normalized = strtolower(trim($query));
        $results = [];

        foreach ($this->fakultasData as $item) {
            if (strpos(strtolower($item['fakultas']), $normalized) !== false ||
                strpos(strtolower($item['fakul']), $normalized) !== false) {
                $results[] = $item;
            }
        }

        return $results;
    }
}