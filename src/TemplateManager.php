<?php

namespace Deadbeef;

use Deadbeef\Context\ApplicationContext;
use Deadbeef\Entity\Quote;
use Deadbeef\Entity\Template;
use Deadbeef\Entity\User;
use Deadbeef\Repository\DestinationRepository;
use Deadbeef\Repository\QuoteRepository;
use Deadbeef\Repository\SiteRepository;

class TemplateManager
{
    private $appContext;
    private $quoteRepo;
    private $siteRepo;
    private $destinationRepo;

    public function __construct(
        ApplicationContext $appContext,
        QuoteRepository $quoteRepo,
        SiteRepository $siteRepo,
        DestinationRepository $destinationRepo
    ) {
        $this->appContext = $appContext;
        $this->quoteRepo = $quoteRepo;
        $this->siteRepo = $siteRepo;
        $this->destinationRepo = $destinationRepo;
    }

    /**
     * Render the given template against the provided data. It mostly performs replacements in the template
     * subject and content
     *
     * @param Template $tpl
     * @param array $data could contains a 'user' and a 'quote' key
     *
     * @return Template the template to render, a clone will be returned to preserve immutability
     */
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }
        $user = (isset($data['user']) and ($data['user'] instanceof User)) ?
            $data['user'] :
            $this->appContext->getCurrentUser();
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        $templateCopy = clone($tpl);
        $templateCopy->subject = $this->renderText($templateCopy->subject, $user, $quote);
        $templateCopy->content = $this->renderText($templateCopy->content, $user, $quote);

        return $templateCopy;
    }

    private function renderText($text, User $user, Quote $quote = null)
    {
        if ($quote) {
            $quote = $this->quoteRepo->getById($quote->id);
            $site = $this->siteRepo->getById($quote->siteId);
            $destination = $this->destinationRepo->getById($quote->destinationId);

            $text = $this->render('[quote:summary_html]', Quote::renderHtml($quote), $text, false);
            $text = $this->render('[quote:summary]', Quote::renderText($quote), $text);
            $text = $this->render('[quote:destination_name]', $destination->countryName, $text);
            $text = $this->render(
                '[quote:destination_link]',
                sprintf('%s/%s/quote/%s', $site->url, $destination->countryName, $quote->id),
                $text
            );
        } else {
            if (preg_match('/\[quote:.*\]/', $text)) {
                throw new MissingContextVarException('quote entity is required to render the given template');
            }
        }

        $text = $this->render('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);

        return $text;
    }

    /**
     * Render the given $subject by replacing $search with $replace
     *
     * @param string $search the string to replace
     * @param string $replace the replacement value
     * @param string $subject the text to render
     * @param bool|true $autoEscape whether or not to escape the given replacement text
     *
     * @return mixed
     */
    private function render(string $search, string $replace, string $subject, bool $autoEscape = true) : string
    {
        $textToInsert = $autoEscape ? htmlspecialchars($replace) : $replace;
        return str_replace($search, $textToInsert, $subject);
    }
}
