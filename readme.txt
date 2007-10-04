Current config:
---------------

	<disks>
		<disk>
			<name>da0</name>
			<fullname>/dev/da0</fullname>
			<harddiskstandby>0</harddiskstandby>
			<acoustic>0</acoustic>
			<apm>0</apm>
			<udma>auto</udma>
			<type>SCSI</type>
			<desc>VMware, VMware Virtual S 1.0</desc>
			<size>103MB</size>
			<fstype>geli</fstype>
		</disk>
		<disk>
			<name>da1</name>
			<fullname>/dev/da1</fullname>
			<harddiskstandby>0</harddiskstandby>
			<acoustic>0</acoustic>
			<apm>0</apm>
			<udma>auto</udma>
			<type>SCSI</type>
			<desc>VMware, VMware Virtual S 1.0</desc>
			<size>103MB</size>
			<fstype>geli</fstype>
		</disk>
		<disk>
			<name>da2</name>
			<fullname>/dev/da2</fullname>
			<harddiskstandby>0</harddiskstandby>
			<acoustic>0</acoustic>
			<apm>0</apm>
			<udma>auto</udma>
			<type>SCSI</type>
			<desc>VMware, VMware Virtual S 1.0</desc>
			<size>103MB</size>
			<fstype>ufsgpt</fstype>
		</disk>
	</disks>
	<geli>
		<vdisk>
			<aalgo>none</aalgo>
			<ealgo>AES</ealgo>
			<fullname>/dev/da0.eli</fullname>
			<desc>Encrypted disk</desc>
			<name>da0</name>
			<size>103MB</size>
		</vdisk>
		<vdisk>
			<aalgo>none</aalgo>
			<ealgo>AES</ealgo>
			<fullname>/dev/da1.eli</fullname>
			<desc>Encrypted disk</desc>
			<name>da1</name>
			<size>103MB</size>
		</vdisk>
	</geli>
	<gstripe>
		<vdisk>
			<name>RAID0</name>
			<type>0</type>
			<diskr>/dev/da0</diskr>
			<diskr>/dev/da1</diskr>
			<desc>Software gstripe RAID 0</desc>
			<fullname>/dev/stripe/RAID0</fullname>
		</vdisk>
	</gstripe>

New config:
-----------

	<disks>
		<!-- Real disks. -->
		<disk>
			<name>da0</name>
			<devicename>/dev/da0</devicename> // Convert: fullname -> devicename
			<harddiskstandby>0</harddiskstandby>
			<acoustic>0</acoustic>
			<apm>0</apm>
			<udma>auto</udma>
			<type>SCSI</type>
			<desc>VMware, VMware Virtual S 1.0</desc>
			<size>103MB</size> // Size on MB. (Convert: Remove MB from string).
			<fstype>geli</fstype>
		</disk>
		<!-- Virtual disks. -->
		<vdisk>
			<name>xxx</name>
			<devicename>xxx</devicename> // Device name, e.g. /dev/stripe/RAID0, /dev/da0.eli or /dev/da1.eli
			<type>gvinum|gmirror|gconcat|gstripe|graid5|geli</type> // Type of virtual disk
			// Additional attributes, depending on 'type'. They are only added for the specific type.
			// geli:
			<aalgo>none</aalgo>
			<ealgo>AES</ealgo>
			// gstripe:
			<type>0</type>
			<diskr>/dev/da0</diskr>
			<diskr>/dev/da1</diskr>
		</vdisk>
	</disks>
