<?php

namespace App\Services;

class NsrpSkillService
{
    /**
     * Standard 21st Century Skills per DOLE NSRP Form 1.REV 3
     */
    public static function get21stCenturySkills(): array
    {
        return [
            'Innovation',
            'Team Work',
            'Multitasking',
            'Work Ethics',
            'Self Motivation',
            'Creative Problem Solving',
            'Problem Solving',
            'Critical Thinking',
            'Decision Making',
            'Stress Tolerance',
            'Planning and Organizing',
            'Social Perceptiveness',
            'English Functional Skills',
            'English Comprehension',
            'Math Functional Skill',
        ];
    }

    /**
     * Standard Technical / Vocational Skills per DOLE NSRP Form 1.REV 3
     */
    public static function getTechnicalSkills(): array
    {
        return [
            'Carpentry',
            'Masonry',
            'Welding',
            'Auto Mechanic',
            'Plumbing',
            'Driving',
            'Gardening',
            'Tailoring',
            'Photography',
            'Hairdressing',
            'Cooking',
            'Baking',
            'Electrical Installation & Maintenance',
            'Electronics & Hardware Troubleshooting',
            'Housekeeping & Hospitality Operations',
            'Food & Beverage Service',
            'Customer Care & BPO Communication',
            'Information Technology & Software',
            'Accounting & Bookkeeping Support',
            'Healthcare Assistance & First Aid',
        ];
    }

    /**
     * Complete list of all standardized NSRP skills
     */
    public static function getAllStandardSkills(): array
    {
        return array_merge(self::get21stCenturySkills(), self::getTechnicalSkills());
    }

    /**
     * Clean and format selected NSRP skills (including custom "Others" input)
     */
    public static function formatSkills(array $skills, ?string $otherSkill = null): string
    {
        $cleaned = [];
        foreach ($skills as $s) {
            $s = trim($s);
            if (!empty($s) && !in_array($s, $cleaned)) {
                $cleaned[] = $s;
            }
        }

        if (!empty($otherSkill)) {
            $otherParts = preg_split('/[\r\n,;]+/', $otherSkill);
            foreach ($otherParts as $op) {
                $op = trim($op);
                if (!empty($op) && !in_array($op, $cleaned)) {
                    $cleaned[] = $op;
                }
            }
        }

        return implode("\n", $cleaned);
    }
}
