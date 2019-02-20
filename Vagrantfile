# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "centos/7"
  config.vm.box_check_update = true
  config.vm.host_name = "semlohe-develbox"

  #: NETWORK
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 443, host: 8443
  config.vm.network "forwarded_port", guest: 3306, host: 3306

  config.vm.network "public_network", use_dhcp_assigned_default_route: true
  config.vm.network "private_network", ip: "192.168.33.10"

  config.vm.synced_folder ".",                "/home/vagrant/public_html/semlohe"

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false
    vb.memory = "1024"
    vb.cpus = "2"
  end

  config.vm.provision "shell", path: "etc/provision/initialize.sh", privileged: true
end

