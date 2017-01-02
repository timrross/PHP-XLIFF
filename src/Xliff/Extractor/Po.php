<?php

namespace Timrross\Xliff\Extractor;

use Timrross\Xliff\Document;

class Po
{
    public static function extract($po)
    {
        if (!$po instanceof \PO) {
            return false;
        }

        $xliff = new Document();
        $xliff->set_source_locale(get_locale());
        $xliff->set_target_locale('%%%%trgLang%%%%');
        $xliff->file(true)->set_attribute('id', $po->headers['Project-Id-Version']);

        $create_new_notes = true;
        $count = 0;

        foreach ($po->entries as $entry) {
            $xliff->file()->unit(true)->set_attribute('id', 'entry-'.$count);

            if (!empty($entry->context)) {
                $xliff->file()->unit()->notes(true)->note(true)->set_text_content($entry->context);
                $create_new_notes = false;
            }

            if (!empty($entry->extracted_comments)) {
                $xliff->file()->unit()->notes($create_new_notes)->note(true)->set_text_content($entry->extracted_comments);
            }

            $xliff->file()->unit()->segment(true)->source(true)->set_text_content($entry->singular);
            $xliff->file()->unit()->segment()->target(true);

            $create_new_notes = true;
            ++$count;
        }

        return $xliff;
    }
}
