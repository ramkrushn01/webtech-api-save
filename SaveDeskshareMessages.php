<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HelpdeskMessages;
use App\Mailbox;
use App\Thread;
use App\Conversation;
use App\Folder;
use App\Customer;
use App\Email;

class SaveDeskshareMessages extends Controller
{
    public function test()
    {
        return "API is working";
    }

    public function save(Request $request)
    {
        $data = $request->post();

        try {
            $name = $data['name'];
            $email = $data['email'];
            $product_name = $data['product_name'];
            $product_version = $data['product_version'];
            $message = $data['message'];

            $mailbox_id = 1;
        } catch (\Throwable $th) {
            return array("status" => false, "message" => "Data Insufficient");
        }


        // for creating the new customer
        try {
            $customer = new Customer;
            $customer->first_name = $name;
            $customer->save();
        } catch (\Throwable $th) {
            return array("status" => false, "message" => "Error with creating new customer");
        }


        // for creating new email
        try {
            $email_table = new Email;
            $email_table->customer_id = $customer->id;
            $email_table->email = $email;
            $email_table->type = 1;
            $email_table->save();
        } catch (\Exception $th) {
            // return array("status"=>false,"message"=>"Creating with new email");
        }



        try {
            $conversation = new Conversation;
            $conversation->threads_count = 1;
            $conversation->type = 1;
            $conversation->folder_id = 3;
            $conversation->status = 2;
            $conversation->state = 2;
            $conversation->subject = $product_name;
            $conversation->customer_email = $email;
            $conversation->cc = $email;
            $conversation->preview = substr($message, 0, 250);
            $conversation->mailbox_id = $mailbox_id;
            $conversation->user_id = 1; // is hard-code for the 1 created user
            $conversation->customer_id = $customer->id;
            $conversation->source_via = 2;
            $conversation->source_type = 2;
            $conversation->save();
        } catch (\Throwable $th) {
            return array("status" => false, "message" => "Error with creating new customer");
        }


        // return $conversation->id;
        try {
            $thread = new Thread;
            $thread->conversation_id = $conversation->id;
            $thread->type = 2;
            $thread->status = 2;
            $thread->state = 2;
            $thread->body = $message;
            $thread->to = $email;
            $thread->has_attachments = 0;
            $thread->source_via = 2;
            $thread->source_type = 2;
            $thread->first = 1;
            $thread->imported = 0;
            $thread->save();
        } catch (\Throwable $th) {
            return array("status" => false, "message" => "Error with creating new Conversation");
        }

        try {
            $helpdeskMessages = new HelpdeskMessages;
            $helpdeskMessages->product_name = $product_name;
            $helpdeskMessages->product_version = $product_version;
            $helpdeskMessages->name = $name;
            $helpdeskMessages->email = $email;
            $helpdeskMessages->message = $message;
            $helpdeskMessages->conversation_id = $conversation->id;
            $helpdeskMessages->save();

        } catch (\Throwable $th) {
            return array("status" => false, "message" => "Error with creating new HelpdeskMessage");
        }

        return array("status" => true, "message" => "Message Send Successfully");

    }

}
