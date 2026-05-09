<?php
require_once __DIR__ . "/../../config/database.php";

class Festival {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new festival
     */
    public function create($name, $date, $description = '', $image_url = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO festivals (name, date, description, image_url) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssss", $name, $date, $description, $image_url);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Festival created successfully", "id" => $insertId] :
                ["success" => false, "message" => "Failed to create festival"];
        } catch (Exception $e) {
            error_log("Festival error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while creating festival"];
        }
    }

    /**
     * Get all festivals
     */
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM festivals ORDER BY date ASC");
        return $result;
    }

    /**
     * Get festivals by year.
     */
    public function getByYear($year) {
        $stmt = $this->conn->prepare("SELECT * FROM festivals WHERE YEAR(date) = ? ORDER BY date ASC");
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Get upcoming festivals only
     */
    public function getUpcoming() {
        $result = $this->conn->query("SELECT * FROM festivals WHERE date >= CURDATE() ORDER BY date ASC");
        return $result;
    }

    /**
     * Unified festival-page feed:
     * - upcoming festivals (database)
     * - upcoming auspicious/special days (special_days table)
     * - curated Sri Lankan pooja/auspicious observances (static recurring list)
     */
    public function getFestivalPageItems($selectedDate = null) {
        $items = [];
        $selectedDate = is_string($selectedDate) ? trim($selectedDate) : null;
        $hasDateFilter = !empty($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate);

        // DB festivals
        if ($hasDateFilter) {
            $stmt = $this->conn->prepare("SELECT id, name, date, description FROM festivals WHERE date = ?");
            $stmt->bind_param("s", $selectedDate);
            $stmt->execute();
            $festivals = $stmt->get_result();
            $stmt->close();
        } else {
            $festivals = $this->conn->query("SELECT id, name, date, description FROM festivals WHERE date >= CURDATE()");
        }
        if ($festivals) {
            while ($row = $festivals->fetch_assoc()) {
                $items[] = [
                    'id' => (int) $row['id'],
                    'name' => $row['name'],
                    'date' => $row['date'],
                    'description' => $row['description'],
                    'category' => 'Festival',
                    'editable' => true
                ];
            }
        }

        // DB special days / auspicious days
        if ($hasDateFilter) {
            $specialStmt = $this->conn->prepare("SELECT id, title, day_date, description FROM special_days WHERE day_date = ?");
            $specialStmt->bind_param("s", $selectedDate);
            $specialStmt->execute();
            $specialDays = $specialStmt->get_result();
            $specialStmt->close();
        } else {
            $specialDays = $this->conn->query("SELECT id, title, day_date, description FROM special_days WHERE day_date >= CURDATE()");
        }
        if ($specialDays) {
            while ($row = $specialDays->fetch_assoc()) {
                $items[] = [
                    'id' => null,
                    'name' => $row['title'],
                    'date' => $row['day_date'],
                    'description' => $row['description'],
                    'category' => 'Auspicious Day',
                    'editable' => false
                ];
            }
        }

        // Curated Sri Lankan recurring observances
        if ($hasDateFilter) {
            $filterYear = (int) date('Y', strtotime($selectedDate));
            $items = array_merge($items, $this->getCuratedSriLankanObservances($filterYear));
            $items = array_filter($items, function ($item) use ($selectedDate) {
                return !empty($item['date']) && $item['date'] === $selectedDate;
            });
        } else {
            $currentYear = (int) date('Y');
            $items = array_merge($items, $this->getCuratedSriLankanObservances($currentYear));
            $items = array_merge($items, $this->getCuratedSriLankanObservances($currentYear + 1));

            // Keep upcoming only
            $today = date('Y-m-d');
            $items = array_filter($items, function ($item) use ($today) {
                return !empty($item['date']) && $item['date'] >= $today;
            });
        }

        usort($items, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return array_values($items);
    }

    /**
     * Get festival by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM festivals WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $festival = $result->fetch_assoc();
        $stmt->close();
        return $festival;
    }

    /**
     * Update festival
     */
    public function update($id, $name, $date, $description = '') {
        try {
            $stmt = $this->conn->prepare("UPDATE festivals SET name = ?, date = ?, description = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("sssi", $name, $date, $description, $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Update festival error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a festival
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM festivals WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Delete festival error: " . $e->getMessage());
            return false;
        }
    }

    private function getCuratedSriLankanObservances($year) {
        $observances = [
            ['01-14', 'Thai Pongal Pooja', 'Harvest thanksgiving pooja with pongal offering.', 'Special Pooja Day'],
            ['01-15', 'Thiruvalluvar Day Blessing Pooja', 'Prayer day honoring Tamil wisdom traditions.', 'Special Pooja Day'],
            ['02-04', 'Independence Day Temple Blessings', 'Special prayers for the nation and harmony.', 'Special Pooja Day'],
            ['02-26', 'Maha Shivaratri', 'Night-long Shiva abhishekam and chanting.', 'Festival'],
            ['04-13', 'Sinhala and Tamil New Year Eve Pooja', 'Blessings before New Year transitions.', 'Festival'],
            ['04-14', 'Sinhala and Tamil New Year', 'Auspicious New Year temple rituals.', 'Festival'],
            ['05-12', 'Vesak Full Moon Observance', 'Spiritual observance day with prayers and dana.', 'Auspicious Day'],
            ['06-11', 'Poson Full Moon Observance', 'Day of merit and special temple programs.', 'Auspicious Day'],
            ['07-10', 'Esala Full Moon Observance', 'Special full moon prayers and lamp offerings.', 'Auspicious Day'],
            ['07-25', 'Aadi Amavasai Tharpanam Pooja', 'Ancestor remembrance and special offerings.', 'Special Pooja Day'],
            ['08-08', 'Varalakshmi Vratham', 'Prosperity pooja dedicated to Goddess Lakshmi.', 'Special Pooja Day'],
            ['08-16', 'Nikini Full Moon Observance', 'Auspicious full moon worship and almsgiving.', 'Auspicious Day'],
            ['08-27', 'Vinayagar Chathurthi', 'Special Ganapathi homam and sankatahara prayers.', 'Festival'],
            ['09-14', 'Binara Full Moon Observance', 'Devotional observance focused on spiritual discipline.', 'Auspicious Day'],
            ['09-22', 'Navaratri Begins', 'Nine nights of Amman worship, alankaram and poojas.', 'Festival'],
            ['10-01', 'Saraswathi Pooja', 'Special pooja for knowledge, arts and learning.', 'Special Pooja Day'],
            ['10-02', 'Vijayadashami', 'Auspicious day for new beginnings and vidyarambam.', 'Festival'],
            ['10-13', 'Vap Full Moon Observance', 'Full moon day observances and meditation programs.', 'Auspicious Day'],
            ['10-20', 'Deepavali', 'Festival of lights with Lakshmi pooja and celebrations.', 'Festival'],
            ['11-12', 'Ill Full Moon Observance', 'Auspicious full moon day for prayer and charity.', 'Auspicious Day'],
            ['12-11', 'Unduvap Full Moon Observance', 'Year-end full moon observance with prayers.', 'Auspicious Day'],
            ['12-31', 'Year-End Special Pooja', 'Thanksgiving pooja and blessings for the coming year.', 'Special Pooja Day']
        ];

        $items = [];
        foreach ($observances as $entry) {
            $date = sprintf('%04d-%s', $year, $entry[0]);
            $items[] = [
                'id' => null,
                'name' => $entry[1],
                'date' => $date,
                'description' => $entry[2],
                'category' => $entry[3],
                'editable' => false
            ];
        }

        return $items;
    }
}
