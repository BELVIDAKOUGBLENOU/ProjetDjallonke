import 'package:isar/isar.dart';

part 'evidence_file.g.dart';

@Collection()
class EvidenceFile {
  Id id = Isar.autoIncrement;

  int? eventId;
  String? url;
  String? fileType;
  String? uid;
  int? version;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final event = IsarLink<dynamic>();

  EvidenceFile({this.eventId, this.url, this.fileType, this.uid, this.version});

  factory EvidenceFile.fromJson(Map<String, dynamic> json) {
    return EvidenceFile(
      eventId: json['event_id'] as int?,
      url: json['url'] as String?,
      fileType: json['file_type'] as String?,
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_id': eventId,
      'url': url,
      'file_type': fileType,
      'uid': uid,
      'version': version,
    };
  }
}
