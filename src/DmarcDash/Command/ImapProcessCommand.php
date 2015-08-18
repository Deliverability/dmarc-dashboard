<?php



namespace DmarcDash\Command;



use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



class ImapProcessCommand extends ContainerAwareCommand
{



    /**
     * Imap connection handle
     */
    var $imapConn = false;
    var $Mailbox = false;



    /**
     * Register this command
     */
    protected function configure ()
    {
        $this
            ->setName('dmarcdash:imap:process')
            ->setDescription('Greet someone')
            ->addArgument(
                'userId',
                InputArgument::OPTIONAL,
                'If only one user, whose IMAP do you want to process?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }



    /**
     * Execute it
     */
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        // Get Users repo service
        $URepo = $this->getContainer()->get('Core')->getModelRepository('User');
        $RRepo = $this->getContainer()->get('Core')->getModelRepository('Report');

        // One particular user or all of them?
        $userId = $input->getArgument('userId');
        if ($userId) {
            $users = array();
        } else {
            $users = $URepo->findAll();
        }


        // Process each user
        $usersCount = count($users);
        $output->writeln("Processing $usersCount IMAP accounts");

        $emailsProcessed  = 0;
        $emailsDeleted    = 0;
        $reportsProcessed = 0;
        foreach ($users as $User) {
            $output->writeln("Processing user ". $User->email .":");

            // Connect to imap
            $this->imapConnect($User);

            // Get messages
            $msgUids = $this->imapGetMessageUids();
            $output->writeln("  Messages found: ". count($msgUids));

            // Process each message
            foreach ($msgUids as $msgUid) {
                $emailsProcessed++;
                $output->writeln("  Processing message uid=$msgUid:");

                // Get content
//                $msgContent = $this->imapGetMessageContent($msgUid);
                $Message = $this->imapGetMessageContent($msgUid);

                // Validate content with DKIM
/*TODO
                $dkimValidator = new \Teon\DKIM\Validator($msgContent);
                $res = $dkimValidator->validateBoolean();
                if (!$res) {
                    $output->writeln("    ERROR: DKIM signature is INVALID! Skipping this message.");
                    continue;
                }
                $output->writeln("    DKIM signature valid.");
*/

                // Get attachment
                $atts = $Message->getAttachments();
                if (count($atts) == 0) {
                    $output->writeln("    WARNING: Skipping message, no attachments found!");
                    continue;
                }
                if (count($atts) > 1) {
                    $output->writeln("    WARNING: Skipping message, too many attachments!");
                    continue;
                }
                $att = array_pop($atts);
                $output->writeln("    Got single attachment, name: $att->filePath");


                // Validate
                $form = $RRepo->getUploadForm(array());
                $falseOrParsedReport = $RRepo->isUploadedFileValidForUser($att->filePath, $User, basename($att->filePath), $form);
                if (!$falseOrParsedReport) {
                    $output->writeln("    WARNING: Skipping report file, not valid");
                    continue;
                }
                $parsedReport = $falseOrParsedReport;

                // Parse
                $RRepo->createReportFromParsedReport($parsedReport);
                $output->writeln("    Report processed.");
                $reportsProcessed++;

                // Delete TMP file
                unlink($att->filePath);
                $output->writeln("    Tmp file deleted.");

                // Delete from imap
                $this->imapMessageDelete($msgUid);
                $output->writeln("    Message removed from IMAP server.");
                $emailsDeleted++;
            }

            // Close the connection
            $this->imapDisconnect();
        }

        $output->writeln("All done, $usersCount users processed.");
    }


    /**
     * Connect to imap server
     *
     * @param    User
     * @return   ZF2 imap connection handle
     */
    protected function imapConnect ($User)
    {
        $imapConfig = array(
            'user'     => $User->imapUsername,
            'password' => $User->imapPassword,
        );

        // Check it with IMAP and delete it
        $imapConnString = '{'. $User->imapHost .':'. $User->imapPort .'/imap/'. $User->imapProtocol .'/novalidate-cert}';
//        $imapConn       = imap_open($imapConnString, $User->imapUsername, $User->imapPassword);

//        $this->imapConn = $imapConn;

        $Mailbox = new \PhpImap\Mailbox($imapConnString, $User->imapUsername, $User->imapPassword, __DIR__.'/../../../var/tmp');
        $this->Mailbox = $Mailbox;
    }



    /**
     * Retrieve messages
     *
     * @param
     * @return   array of message uids
     */
    protected function imapGetMessageCount ()
    {
//        return imap_num_msg($this->imapConn);
        return $this->Mailbox->countMails();
    }



    /**
     * Retrieve messages
     *
     * @param
     * @return   array of message uids
     */
    protected function imapGetMessageUids ()
    {
//        return imap_search($this->imapConn, 'ALL', SE_UID);
        return $this->Mailbox->searchMailBox('ALL');
    }



    /**
     * Convert UID => msgNum
     *
     * @param    string   Message UID
     * @return   int
     */
    protected function imapConvertUidToNum ($msgUid)
    {
        return imap_msgno($this->imapConn, $msgUid);
    }



    /**
     * Get content of a particular message
     *
     * @param    Message id
     * @return   void
     */
    protected function imapGetMessageContent ($msgUid)
    {
/*
        $msgNo = $this->imapConvertUidToNum($msgUid);

        $headers = imap_fetchheader($this->imapConn, $msgNo);
        $body    = imap_body       ($this->imapConn, $msgNo);

//        $msgContent = $headers.$body;
        $msgContent = $body;

        return $msgContent;
*/

        return $this->Mailbox->getMail($msgUid);
    }



    /**
     * Delete particular message
     *
     * @param    Message id
     * @return   void
     */
    protected function imapMessageDelete ($msgUid)
    {
//        imap_delete($this->imapConn, $msgId);
        $this->Mailbox->deleteMail($msgUid);
    }



    /**
     * Disconnect
     *
     * @param
     * @return   void
     */
    protected function imapDisconnect ()
    {
        //imap_expunge($this->imapConn);
//        imap_close($this->imapConn);

        // With this wrapper expunge is done automatically
        unset($this->Mailbox);
    }
}
