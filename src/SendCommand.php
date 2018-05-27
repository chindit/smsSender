<?php

namespace App;

use Ovh\Sms\SmsApi;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * smsSender : Copyright © 2018 Chindit
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * First generated : 05/26/2018 at 18:03
 */

class SendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sms:send')
            ->setDescription('Send a SMS')
            ->setHelp('This command allows you to send a SMS to a specific user')
            ->addArgument('text', InputArgument::REQUIRED, 'Content of the SMS');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = getenv('SMS_RECEIVER');

        if (!$input->getArgument('text') || empty($input->getArgument('text'))) {
            $output->writeln('<error>A message content is required</error>');
            return;
        }

        $output->writeln('<comment>'.$input->getArgument('text').'</comment>');
        $output->writeln($destination);
        $sms = new SmsApi(getenv('SMS_APPLICATION_KEY'),
            getenv('SMS_APPLICATION_SECRET'),
            getenv('SMS_API_ENDPOINT'),
            getenv('SMS_CONSUMER_KEY'));

        $accounts = $sms->getAccounts();
        $sms->setAccount($accounts[0]);

        try {
            $message = $sms->createMessage(false);
            $message->addReceiver($destination);
            $message->setIsMarketing(false);
            $message->send($input->getArgument('text'));
            $output->writeln('<info>Message sent successfully</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to send message: ' . $e->getMessage() . '</error>');
        }
    }
}
